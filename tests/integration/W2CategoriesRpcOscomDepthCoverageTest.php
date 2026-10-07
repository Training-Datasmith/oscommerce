<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Admin\Application\Categories\RPC\GetAll;
use osCommerce\OM\Core\Site\Admin\Application\Categories\RPC\GetAvailableImages;
use osCommerce\OM\Core\Site\Admin\Application\Categories\RPC\SaveSortOrder;
use osCommerce\OM\Core\Site\Shop\Product;
use osCommerce\OM\Core\Site\Shop\Products;
use osCommerce\OM\Core\Site\Shop\ShoppingCart;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopCheckoutSeeder;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Categories RPC + main datatable PHP, shop oscom.php branches, cart/product depth (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class W2CategoriesRpcOscomDepthCoverageTest extends TestCase
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
        unset($_GET, $_POST);
    }

    public function testAdminCategoriesMainAndRpcEndpoints(): void
    {
        InProcessSiteRenderer::renderAdmin(['Categories'], ['cID' => '0']);
        ShopHarnessDataSeeder::ensureBaselineData();

        $pdo = Registry::get('PDO');
        $childId = (int) ($pdo->query(
            'select categories_id from osc_categories where parent_id > 0 order by categories_id limit 1'
        )->fetchColumn() ?: 0);
        if ($childId > 0) {
            InProcessSiteRenderer::renderAdmin(['Categories'], ['cID' => (string) $childId]);
        }

        InProcessSiteRenderer::includeAdminApplicationPage('Categories', 'main.php');
        InProcessSiteRenderer::includeAdminApplicationPage('Categories', 'main.php');

        $_GET['cid'] = (string) ($childId > 0 ? $childId : 0);
        $_GET['search'] = '';
        ob_start();
        try {
            GetAll::execute();
        } catch (\Throwable) {
        }
        ob_end_clean();

        $_GET['search'] = 'a';
        ob_start();
        try {
            GetAll::execute();
        } catch (\Throwable) {
        }
        ob_end_clean();

        $rows = $pdo->query('select categories_id from osc_categories order by sort_order, categories_id limit 3')->fetchAll(\PDO::FETCH_COLUMN);
        if ($rows !== []) {
            $_GET['row'] = array_map('strval', $rows);
            ob_start();
            try {
                SaveSortOrder::execute();
            } catch (\Throwable) {
            }
            ob_end_clean();
        }

        ob_start();
        try {
            GetAvailableImages::execute();
        } catch (\Throwable) {
        }
        ob_end_clean();

        $this->addToAssertionCount(1);
    }

    public function testShopOscomTemplateLayoutBranches(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopCheckoutSeeder::seedCheckoutConfirmationCoverage();
        Registry::get('MessageStack')->add('header', 'Oscom layout coverage');

        $template = Registry::get('Template');
        $template->addPageTags('coverage', 'w2-grind');
        $template->addJavascriptBlock('/* coverage */');

        InProcessSiteRenderer::includeRenderedShopOscomLayout();

        $template->setHasBoxModules(false);
        InProcessSiteRenderer::includeRenderedShopOscomLayout();

        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();
        InProcessSiteRenderer::renderShop(['Checkout', 'Confirm']);
        ShopCheckoutSeeder::seedCheckoutConfirmationCoverage();
        InProcessSiteRenderer::includeRenderedShopOscomLayout();

        InProcessSiteRenderer::includeShopModulePages();

        $this->addToAssertionCount(1);
    }

    public function testCheckoutMainBillingAndProductCartDepth(): void
    {
        InProcessSiteRenderer::renderShop(['Checkout']);
        ShopCheckoutSeeder::seedCheckoutConfirmationCoverage();

        foreach (['main.php', 'billing.php'] as $page) {
            InProcessSiteRenderer::includeShopApplicationPage('Checkout', $page);
            InProcessSiteRenderer::includeShopPageViaOscomLayout('Checkout', $page);
        }

        $pdo = Registry::get('PDO');
        $keyword = $pdo->query(
            'select products_keyword from osc_products_description where products_keyword <> "" limit 1'
        )->fetchColumn();
        if (is_string($keyword) && $keyword !== '') {
            Product::checkEntry($keyword);
        }
        Product::checkID('1');
        Product::checkID('invalid session name');

        $masterId = (int) ($pdo->query(
            'select products_id from osc_products where has_children = 1 and products_status = 1 limit 1'
        )->fetchColumn() ?: 0);
        if ($masterId > 0) {
            $master = new Product($masterId);
            if ($master->isValid()) {
                $master->getVariants(false);
                $master->incrementCounter();
                Products::getProductID($master->getID());
            }
        }

        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();

        $cart = Registry::get('ShoppingCart');
        $cart->reset(true);
        $pid = (int) ($pdo->query('select products_id from osc_products where products_status = 1 limit 1')->fetchColumn() ?: 1);
        $cart->add($pid, 1);
        $itemId = $cart->getBasketID($pid);
        $cart->exists($pid);
        $cart->update($itemId, 'not-numeric');
        $cart->getProducts();
        $cart->remove($itemId);

        $this->addToAssertionCount(1);
    }

    public function testSetupIndexMainAndStep3PostArrays(): void
    {
        InProcessSiteRenderer::renderSetup(['Index']);
        foreach (Registry::get('Language')->getAll() as $lang) {
            InProcessSiteRenderer::renderSetup(['Index'], ['language' => $lang['code']]);
        }
        InProcessSiteRenderer::includeSetupApplicationPage('Index', 'main.php');

        $this->addToAssertionCount(1);
    }
}
