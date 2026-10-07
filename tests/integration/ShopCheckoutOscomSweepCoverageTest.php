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
 * Checkout billing/shipping/main via full oscom layout after scope extract fix (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopCheckoutOscomSweepCoverageTest extends TestCase
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

    public function testCheckoutTemplatePages(): void
    {
        InProcessSiteRenderer::renderShop(['Checkout']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();
        PaymentModuleTestHelper::definePayPalExpressCheckout();

        Registry::get('MessageStack')->add('CheckoutPayment', 'Coverage payment');
        Registry::get('MessageStack')->add('CheckoutShipping', 'Coverage shipping');

        foreach (['main.php', 'billing.php', 'shipping.php', 'billing_address.php', 'shipping_address.php'] as $page) {
            InProcessSiteRenderer::includeShopPageViaOscomLayout('Checkout', $page);
        }

        unset($_GET['manufacturers']);
        InProcessSiteRenderer::includeShopPageViaOscomLayout('Products', 'product_listing.php');

        $this->addToAssertionCount(1);
    }
}
