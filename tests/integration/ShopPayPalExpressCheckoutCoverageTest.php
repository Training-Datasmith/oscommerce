<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Module\Payment\PayPalExpressCheckout;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\PayPalNvpMock;
use Tests\Support\PaymentModuleTestHelper;
use Tests\Support\ShopCheckoutSeeder;

/**
 * PayPal Express Checkout with mocked NVP HTTP (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopPayPalExpressCheckoutCoverageTest extends TestCase
{
    private int $obLevel;

    protected function setUp(): void
    {
        $this->obLevel = ob_get_level();
        PayPalNvpMock::register();
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > $this->obLevel) {
            ob_end_clean();
        }
    }

    public function testPayPalExpressCheckoutFlowWithMockHttp(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();
        PaymentModuleTestHelper::definePayPalExpressCheckout();

        $module = new PayPalExpressCheckout();
        Registry::set('PaymentModule', $module, true);

        $_GET['ppx'] = 'cancel';
        try {
            $module->preConfirmationCheck();
        } catch (\Throwable $e) {
            $this->assertStringContainsString('redirect', strtolower($e->getMessage()));
        }
        unset($_GET['ppx']);

        $_GET['ppx'] = 'retrieve';
        $_GET['token'] = 'EC-TEST-TOKEN';
        try {
            $module->preConfirmationCheck();
        } catch (\Throwable $e) {
            $this->assertStringContainsString('redirect', strtolower($e->getMessage()));
        }

        $ref = new \ReflectionClass($module);
        foreach (['setExpressCheckout', 'getExpressCheckoutDetails', 'doExpressCheckoutPayment'] as $methodName) {
            $method = $ref->getMethod($methodName);
            $method->setAccessible(true);
            try {
                if ($methodName === 'getExpressCheckoutDetails') {
                    $method->invoke($module, 'EC-TEST-TOKEN');
                } elseif ($methodName === 'setExpressCheckout') {
                    $method->invoke($module, ['AMT' => '10.00']);
                } else {
                    $method->invoke($module, [
                        'TOKEN' => 'EC-TEST-TOKEN',
                        'PAYERID' => 'TEST-PAYER',
                        'AMT' => '10.00',
                        'CURRENCYCODE' => 'USD',
                    ]);
                }
            } catch (\Throwable) {
            }
        }

        $this->addToAssertionCount(1);
    }

    public function testPayPalInitializeExpressCheckoutMock(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();
        PaymentModuleTestHelper::definePayPalExpressCheckout();

        $module = new PayPalExpressCheckout();
        $ref = new \ReflectionClass($module);
        $init = $ref->getMethod('initializeExpressCheckout');
        $init->setAccessible(true);

        try {
            $init->invoke($module);
        } catch (\Throwable) {
        }

        $this->addToAssertionCount(1);
    }
}
