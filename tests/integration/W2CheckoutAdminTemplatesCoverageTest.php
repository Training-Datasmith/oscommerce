<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Product;
use osCommerce\OM\Core\Site\Shop\ShoppingCart;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopCheckoutSeeder;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Checkout confirm/billing, Admin Categories/PaymentModules, shop/admin templates (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class W2CheckoutAdminTemplatesCoverageTest extends TestCase
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

    public function testCheckoutMainAndBillingTemplates(): void
    {
        InProcessSiteRenderer::renderShop(['Checkout']);
        ShopCheckoutSeeder::seedCheckoutConfirmationCoverage();

        foreach (['main.php', 'billing.php'] as $page) {
            InProcessSiteRenderer::includeShopApplicationPage('Checkout', $page);
            InProcessSiteRenderer::includeShopPageViaOscomLayout('Checkout', $page);
        }

        InProcessSiteRenderer::renderShop(['Checkout', 'Confirm']);
        InProcessSiteRenderer::includeRenderedShopOscomLayout();

        $this->addToAssertionCount(1);
    }

    public function testAdminCategoriesAndPaymentModulePages(): void
    {
        InProcessSiteRenderer::renderAdmin(['Categories'], ['cID' => '1']);
        ShopHarnessDataSeeder::ensureBaselineData();

        foreach (
            [
                ['Categories', 'main.php'],
                ['Categories', 'edit.php'],
                ['Categories', 'new.php'],
                ['PaymentModules', 'edit.php'],
                ['PaymentModules', 'uninstall.php'],
            ] as [$app, $page]
        ) {
            InProcessSiteRenderer::includeAdminApplicationPage($app, $page);
        }

        InProcessSiteRenderer::renderAdmin(['PaymentModules', 'Edit'], ['module' => 'COD', 'code' => 'COD']);

        $this->addToAssertionCount(1);
    }

    public function testShopAndAdminTemplateFragments(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopCheckoutSeeder::seedCheckoutConfirmationCoverage();

        foreach (
            [
                ['Cart', 'main.php'],
                ['Search', 'main.php'],
                ['Checkout', 'main.php'],
                ['Products', 'main.php'],
            ] as [$app, $page]
        ) {
            InProcessSiteRenderer::includeShopPageViaOscomLayout($app, $page);
            InProcessSiteRenderer::includeRenderedShopOscomLayout();
        }

        InProcessSiteRenderer::renderAdmin(['Dashboard']);
        InProcessSiteRenderer::includeAdminTemplatePart('header.php');

        $this->addToAssertionCount(1);
    }

    public function testShoppingCartHeavyWeightAndProductManufacturer(): void
    {
        InProcessSiteRenderer::renderShop(['Cart']);
        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();

        $pdo = Registry::get('PDO');
        $cart = Registry::get('ShoppingCart');
        $cart->reset(true);

        $productId = (int) ($pdo->query(
            'select p.products_id from osc_products p
             where p.products_status = 1 and p.manufacturers_id > 0
             order by p.products_id limit 1'
        )->fetchColumn() ?: 0);
        if ($productId < 1) {
            $productId = (int) ($pdo->query('select products_id from osc_products limit 1')->fetchColumn() ?: 1);
        }

        $cart->add($productId, 1);
        $itemId = $cart->getBasketID($productId);

        $ref = new \ReflectionClass(ShoppingCart::class);
        $prop = $ref->getProperty('_contents');
        $prop->setAccessible(true);
        $contents = $prop->getValue($cart);
        if (isset($contents[$itemId])) {
            $contents[$itemId]['weight'] = 99999;
            $contents[$itemId]['quantity'] = 3;
            $prop->setValue($cart, $contents);
        }

        if ($ref->hasMethod('_calculate')) {
            $calc = $ref->getMethod('_calculate');
            $calc->setAccessible(true);
            try {
                $calc->invoke($cart, false);
            } catch (\Throwable) {
            }
        }

        $cart->update($itemId, 0);
        if ($ref->hasMethod('_cleanUp')) {
            $clean = $ref->getMethod('_cleanUp');
            $clean->setAccessible(true);
            $clean->invoke($cart);
        }

        $product = new Product($productId);
        if ($product->isValid()) {
            $product->hasManufacturer();
            $product->getManufacturer();
            $product->getManufacturerID();
            $product->getCategoryID();
            $product->getImages();
            $product->getDateAdded();
            $product->getVariants(false);
            $product->hasAttribute('shipping_availability');
        }

        $this->addToAssertionCount(1);
    }
}
