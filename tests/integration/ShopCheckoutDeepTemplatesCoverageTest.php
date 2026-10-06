<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\PaymentModuleTestHelper;
use Tests\Support\ShopCheckoutSeeder;

/**
 * Checkout page templates with payment module and billing method wired (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopCheckoutDeepTemplatesCoverageTest extends TestCase
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

    public function testCheckoutTemplatesWithPaymentModule(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();
        PaymentModuleTestHelper::definePayPalExpressCheckout();

        foreach (
            [
                ['Checkout', 'main.php'],
                ['Checkout', 'billing.php'],
                ['Checkout', 'shipping.php'],
                ['Checkout', 'billing_address.php'],
                ['Checkout', 'shipping_address.php'],
            ] as [$application, $page]
        ) {
            InProcessSiteRenderer::includeShopPageViaOscomLayout($application, $page);
        }

        InProcessSiteRenderer::renderShop(['Checkout']);
        InProcessSiteRenderer::renderShop(['Checkout', 'Billing']);
        InProcessSiteRenderer::renderShop(['Checkout', 'Shipping']);

        $this->addToAssertionCount(1);
    }
}
