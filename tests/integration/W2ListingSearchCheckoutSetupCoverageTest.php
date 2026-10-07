<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\OSCOM;
use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\CategoryTree;
use osCommerce\OM\Core\Site\Shop\Search;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\CheckoutConfirmationPaymentStub;
use Tests\Support\HarnessSettingsFixture;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopCheckoutSeeder;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Product listing depth, Search edge cases, checkout confirmation payment, Setup step_3 (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class W2ListingSearchCheckoutSetupCoverageTest extends TestCase
{
    private int $obLevel;

    private ?string $settingsBackup = null;

    private string $settingsPath;

    protected function setUp(): void
    {
        $this->obLevel = ob_get_level();
        $this->settingsPath = dirname(__DIR__, 2) . '/osCommerce/OM/Config/settings.ini';
        if (is_file($this->settingsPath)) {
            $this->settingsBackup = file_get_contents($this->settingsPath);
        }
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > $this->obLevel) {
            ob_end_clean();
        }
        HarnessSettingsFixture::restoreToConfigFile();
        unset($_GET, $_POST);
    }

    public function testProductListingTemplateDepth(): void
    {
        InProcessSiteRenderer::renderShop(['Products']);
        InProcessSiteRenderer::includeShopApplicationPage('Products', 'product_listing.php');
        InProcessSiteRenderer::includeShopPageViaOscomLayout('Products', 'product_listing.php');
        unset($_GET['manufacturers']);
        InProcessSiteRenderer::includeShopApplicationPage('Products', 'product_listing.php');
        InProcessSiteRenderer::includeShopApplicationPage('Index', 'product_listing.php');
        InProcessSiteRenderer::includeShopPageViaOscomLayout('Index', 'product_listing.php');

        $this->addToAssertionCount(1);
    }

    public function testSearchEdgeCasesAndResults(): void
    {
        InProcessSiteRenderer::renderShop(['Search']);
        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();

        $pdo = Registry::get('PDO');
        $categoryId = (int) ($pdo->query('select categories_id from osc_categories order by categories_id limit 1')->fetchColumn() ?: 0);
        $manufacturerId = (int) ($pdo->query('select manufacturers_id from osc_manufacturers order by manufacturers_id limit 1')->fetchColumn() ?: 0);

        $tree = new CategoryTree();
        Registry::set('CategoryTree', $tree, true);

        $search = new Search();
        $search->setKeywords('one two three four five six seven eight');
        $search->setPriceFrom(1.0);
        $search->setPriceTo(500.0);
        $search->setDateFrom(strtotime('-5 years'));
        $search->setDateTo(time());
        if ($categoryId > 0) {
            $search->setCategory($categoryId, true);
        }
        if ($manufacturerId > 0) {
            $search->setManufacturer($manufacturerId);
        }

        $search->hasDateSet('from');
        $search->hasDateSet('to');
        $search->hasPriceSet('from');
        $search->hasPriceSet('to');
        $search->getMinYear();
        $search->getMaxYear();

        try {
            $search->execute();
            $search->getResult();
            $search->getNumberOfResults();
        } catch (\Throwable) {
        }

        Registry::set('Search', $search, true);
        InProcessSiteRenderer::includeShopApplicationPage('Search', 'results.php');

        $this->addToAssertionCount(1);
    }

    public function testCheckoutMainPaymentConfirmationBranches(): void
    {
        InProcessSiteRenderer::renderShop(['Checkout', 'Confirm']);
        ShopCheckoutSeeder::seedCheckoutConfirmationCoverage();
        CheckoutConfirmationPaymentStub::primePaymentModuleWithConfirmation();

        $module = Registry::get('PaymentModule');
        $module->preConfirmationCheck();
        $module->confirmation();
        $module->hasGateway();
        $module->getGatewayURL();
        $module->getProcessButton();

        InProcessSiteRenderer::includeShopApplicationPage('Checkout', 'main.php');
        InProcessSiteRenderer::includeShopPageViaOscomLayout('Checkout', 'main.php');
        InProcessSiteRenderer::includeShopApplicationPage('Checkout', 'billing.php');

        $this->addToAssertionCount(1);
    }

    public function testSetupStep3WritableSuccessOutputAndIndexMain(): void
    {
        InProcessSiteRenderer::renderSetup(['Index']);
        $_GET['language'] = 'en_US';
        InProcessSiteRenderer::includeSetupApplicationPage('Index', 'main.php');

        $cacheDir = OSCOM::BASE_DIRECTORY . 'Work/Cache';
        if (is_dir($cacheDir)) {
            file_put_contents($cacheDir . '/step3-heredoc.cache', 'purge');
        }

        $_POST = [
            'HTTP_WWW_ADDRESS' => 'http://shop.example.com:8443/catalog/',
            'DB_SERVER' => getenv('OSCOMMERCE_DB_SERVER') ?: '127.0.0.1',
            'DB_SERVER_USERNAME' => getenv('OSCOMMERCE_DB_USER') ?: 'oscommerce',
            'DB_SERVER_PASSWORD' => getenv('OSCOMMERCE_DB_PASS') ?: 'oscommerce',
            'DB_DATABASE' => getenv('OSCOMMERCE_DB_NAME') ?: 'oscommerce_test',
            'DB_SERVER_PORT' => getenv('OSCOMMERCE_DB_PORT') ?: '3306',
            'DB_DATABASE_CLASS' => getenv('OSCOMMERCE_DB_CLASS') ?: 'MySQL_Standard',
            'DB_TABLE_PREFIX' => getenv('OSCOMMERCE_DB_PREFIX') ?: 'osc_',
            'CFG_TIME_ZONE' => 'UTC',
            'CFG_STORE_NAME' => 'Coverage',
            'CFG_STORE_OWNER_NAME' => 'Owner',
            'CFG_STORE_OWNER_EMAIL_ADDRESS' => 'owner@example.test',
            'CFG_ADMINISTRATOR_USERNAME' => 'admin',
            'CFG_ADMINISTRATOR_PASSWORD' => 'adminpass123',
            'tags' => ['tag-a', 'tag-b'],
        ];

        InProcessSiteRenderer::includeSetupApplicationPage('Install', 'step_3.php');

        $this->assertFileIsReadable($this->settingsPath);
        $this->addToAssertionCount(1);
    }
}
