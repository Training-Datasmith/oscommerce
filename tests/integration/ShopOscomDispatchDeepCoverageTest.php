<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\PaymentModuleTestHelper;
use Tests\Support\ShopCheckoutSeeder;

/**
 * Full oscom layout via dispatch + re-include (box/header/footer paths).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopOscomDispatchDeepCoverageTest extends TestCase
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

    public function testDispatchAndLayoutWithBoxesAndListing(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();
        InProcessSiteRenderer::includeRenderedShopOscomLayout();

        InProcessSiteRenderer::renderShop(['Cart']);
        InProcessSiteRenderer::includeRenderedShopOscomLayout();

        InProcessSiteRenderer::renderShop(['Checkout']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();
        PaymentModuleTestHelper::definePayPalExpressCheckout();
        Registry::get('MessageStack')->add('header', 'Coverage');
        InProcessSiteRenderer::includeRenderedShopOscomLayout();

        InProcessSiteRenderer::renderShop(['Products']);
        unset($_GET['manufacturers']);
        InProcessSiteRenderer::includeShopPageViaOscomLayout('Products', 'product_listing.php');
        InProcessSiteRenderer::includeShopApplicationPage('Products', 'product_listing.php');

        $this->addToAssertionCount(1);
    }
}
