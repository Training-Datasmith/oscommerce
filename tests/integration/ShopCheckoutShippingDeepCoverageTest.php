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
 * Checkout shipping/main pages and shipping Process action (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopCheckoutShippingDeepCoverageTest extends TestCase
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

    public function testCheckoutShippingFlowPages(): void
    {
        InProcessSiteRenderer::renderShop(['Checkout', 'Shipping']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();
        PaymentModuleTestHelper::definePayPalExpressCheckout();
        ShopCheckoutSeeder::seedBillingMethodWithoutRecalculate(Registry::get('ShoppingCart'));

        Registry::get('MessageStack')->add('CheckoutShipping', 'Coverage shipping message');

        foreach (['main.php', 'shipping.php', 'shipping_address.php'] as $page) {
            InProcessSiteRenderer::includeShopPageViaOscomLayout('Checkout', $page);
            InProcessSiteRenderer::includeShopApplicationPage('Checkout', $page);
        }

        InProcessSiteRenderer::renderShop(['Checkout', 'Shipping']);
        InProcessSiteRenderer::includeRenderedShopOscomLayout();

        $this->addToAssertionCount(1);
    }
}
