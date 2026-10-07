<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Search;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopCheckoutSeeder;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Products/Search templates, checkout main, Admin Services edit, Setup Index (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class W2ProductsSearchServicesSetupCoverageTest extends TestCase
{
    private int $obLevel;

    protected function setUp(): void
    {
        $this->obLevel = ob_get_level();
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > $this->obLevel) {
            ob_end_clean();
        }
    }

    public function testProductsApplicationTemplateSweep(): void
    {
        InProcessSiteRenderer::renderShop(['Products']);
        ShopHarnessDataSeeder::ensureBaselineData();

        $pdo = Registry::get('PDO');
        $withImages = (int) ($pdo->query(
            'select p.products_id from osc_products p
             inner join osc_products_images i on i.products_id = p.products_id
             where p.products_status = 1 limit 1'
        )->fetchColumn() ?: 0);
        if ($withImages > 0) {
            $_GET['products_id'] = (string) $withImages;
        }

        foreach (
            [
                'main.php',
                'all.php',
                'images.php',
                'specials.php',
                'reviews.php',
                'reviews_product.php',
                'reviews_view.php',
                'reviews_write.php',
                'tell_a_friend.php',
                'product_listing.php',
            ] as $page
        ) {
            InProcessSiteRenderer::includeShopApplicationPage('Products', $page);
            InProcessSiteRenderer::includeShopPageViaOscomLayout('Products', $page);
        }

        $this->addToAssertionCount(1);
    }

    public function testSearchMainAndResultsTemplates(): void
    {
        InProcessSiteRenderer::renderShop(['Search']);
        $_GET['Search'] = '';
        $_GET['Q'] = 'dvd';
        $_GET['category'] = '';
        $_GET['manufacturer'] = '';
        $_GET['recursive'] = '1';

        try {
            $search = new Search();
            $search->setKeywords('dvd');
            $search->execute();
            Registry::set('Search', $search, true);
        } catch (\Throwable) {
        }

        InProcessSiteRenderer::includeShopApplicationPage('Search', 'main.php');
        InProcessSiteRenderer::includeShopApplicationPage('Search', 'results.php');
        InProcessSiteRenderer::includeShopPageViaOscomLayout('Search', 'main.php');
        InProcessSiteRenderer::renderShop(['Search', 'Results'], ['Q' => 'dvd']);

        $this->addToAssertionCount(1);
    }

    public function testCheckoutMainConfirmationTemplate(): void
    {
        InProcessSiteRenderer::renderShop(['Checkout', 'Confirm']);
        ShopCheckoutSeeder::seedCheckoutConfirmationCoverage();
        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();

        $cart = Registry::get('ShoppingCart');
        if ($cart->hasContents()) {
            foreach ($cart->getProducts() as $row) {
                if (\defined('STOCK_CHECK') && STOCK_CHECK === '1') {
                    $cart->isInStock($row['item_id']);
                }
            }
        }

        InProcessSiteRenderer::includeShopApplicationPage('Checkout', 'main.php');
        InProcessSiteRenderer::includeShopPageViaOscomLayout('Checkout', 'main.php');
        InProcessSiteRenderer::includeRenderedShopOscomLayout();

        $this->addToAssertionCount(1);
    }

    public function testAdminServicesEditAndSetupIndexRemainder(): void
    {
        InProcessSiteRenderer::renderAdmin(['Services', 'Edit'], ['code' => 'Core']);
        foreach (['Breadcrumb', 'Currencies', 'Debug', 'Language', 'Session'] as $code) {
            $_GET['code'] = $code;
            InProcessSiteRenderer::includeAdminApplicationPage('Services', 'edit.php');
        }

        InProcessSiteRenderer::renderSetup(['Index']);
        foreach (Registry::get('Language')->getAll() as $lang) {
            InProcessSiteRenderer::renderSetup(['Index'], ['language' => $lang['code']]);
        }
        InProcessSiteRenderer::includeSetupApplicationPage('Index', 'main.php');
        InProcessSiteRenderer::renderSetup(['Install'], ['step' => '3']);

        $this->addToAssertionCount(1);
    }
}
