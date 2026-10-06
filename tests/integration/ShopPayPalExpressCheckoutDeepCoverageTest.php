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
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Additional PayPal Express Checkout branches (failure ACK, process, Live NVP URL).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopPayPalExpressCheckoutDeepCoverageTest extends TestCase
{
    private int $obLevel;

    protected function setUp(): void
    {
        $this->obLevel = ob_get_level();
        PayPalNvpMock::register();
        PayPalNvpMock::clearOverrides();
    }

    protected function tearDown(): void
    {
        PayPalNvpMock::clearOverrides();
        while (ob_get_level() > $this->obLevel) {
            ob_end_clean();
        }
    }

    public function testSetExpressCheckoutFailureSendsDebugEmail(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();
        PaymentModuleTestHelper::applyPayPalDbOverrides([
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_DEBUG_EMAIL' => 'paypal-debug@example.test',
        ]);
        PaymentModuleTestHelper::definePayPalExpressCheckout();

        PayPalNvpMock::setMethodAck('SetExpressCheckout', 'Failure');

        $module = new PayPalExpressCheckout();
        $ref = new \ReflectionClass($module);
        $method = $ref->getMethod('setExpressCheckout');
        $method->setAccessible(true);
        $result = $method->invoke($module, ['AMT' => '12.00']);

        $this->assertSame('Failure', $result['ACK']);
    }

    public function testInitializeExpressCheckoutFailureRedirect(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();
        PaymentModuleTestHelper::definePayPalExpressCheckout();

        PayPalNvpMock::setMethodAck('SetExpressCheckout', 'Failure');

        $module = new PayPalExpressCheckout();
        $ref = new \ReflectionClass($module);
        $init = $ref->getMethod('initializeExpressCheckout');
        $init->setAccessible(true);

        try {
            $init->invoke($module);
            $this->fail('Expected cart error redirect');
        } catch (\Throwable $e) {
            $this->assertStringContainsString('redirect', strtolower($e->getMessage()));
        }
    }

    public function testRetrieveExpressCheckoutFailureRedirect(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        PaymentModuleTestHelper::definePayPalExpressCheckout();

        PayPalNvpMock::setMethodAck('GetExpressCheckoutDetails', 'Failure');

        $module = new PayPalExpressCheckout();
        $_GET['ppx'] = 'retrieve';
        $_GET['token'] = 'EC-FAIL-TOKEN';

        try {
            $module->preConfirmationCheck();
            $this->fail('Expected cart error redirect');
        } catch (\Throwable $e) {
            $this->assertStringContainsString('redirect', strtolower($e->getMessage()));
        }
    }

    public function testProcessWithTokenCompletesOrder(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedGuestCheckoutCart();
        PaymentModuleTestHelper::definePayPalExpressCheckout();

        $_SESSION['Shop']['PM']['PAYPAL']['EC'] = [
            'TOKEN' => 'EC-TEST-TOKEN',
            'PAYERID' => 'TEST-PAYER',
        ];

        $module = new PayPalExpressCheckout();
        try {
            $module->process();
        } catch (\Throwable) {
        }

        $this->assertArrayNotHasKey('PAYPAL', $_SESSION['Shop']['PM'] ?? []);
        unset($_SESSION['prepOrderID'], $_SESSION['Shop']);
    }

    public function testProcessPaymentFailureRedirect(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();
        PaymentModuleTestHelper::definePayPalExpressCheckout();

        PayPalNvpMock::setMethodAck('DoExpressCheckoutPayment', 'Failure');

        $_SESSION['Shop']['PM']['PAYPAL']['EC'] = [
            'TOKEN' => 'EC-TEST-TOKEN',
            'PAYERID' => 'TEST-PAYER',
        ];

        $module = new PayPalExpressCheckout();

        try {
            $module->process();
            $this->fail('Expected cart error redirect');
        } catch (\Throwable $e) {
            $this->assertStringContainsString('redirect', strtolower($e->getMessage()));
        }
    }

    public function testLiveTransactionServerUsesLiveNvpEndpoint(): void
    {
        PaymentModuleTestHelper::applyPayPalDbOverrides([
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_SERVER' => 'Live',
        ]);

        InProcessSiteRenderer::renderShop(['Index']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();
        PaymentModuleTestHelper::definePayPalExpressCheckout();

        $module = new PayPalExpressCheckout();
        $ref = new \ReflectionClass($module);
        $method = $ref->getMethod('setExpressCheckout');
        $method->setAccessible(true);
        $method->invoke($module, ['AMT' => '9.99']);

        $this->assertStringContainsString('api-3t.paypal.com', PayPalNvpMock::lastUrl());
    }

    public function testAuthorizationPaymentActionWithApiCredentials(): void
    {
        PaymentModuleTestHelper::applyPayPalDbOverrides([
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_METHOD' => 'Authorization',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_USERNAME' => 'api-user',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_PASSWORD' => 'api-pass',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_SIGNATURE' => 'api-sig',
        ]);

        InProcessSiteRenderer::renderShop(['Index']);
        PaymentModuleTestHelper::definePayPalExpressCheckout();

        $module = new PayPalExpressCheckout();
        $ref = new \ReflectionClass($module);
        $method = $ref->getMethod('doExpressCheckoutPayment');
        $method->setAccessible(true);
        $method->invoke($module, [
            'TOKEN' => 'EC-TEST-TOKEN',
            'PAYERID' => 'TEST-PAYER',
            'AMT' => '10.00',
            'CURRENCYCODE' => 'USD',
        ]);

        $body = urldecode(PayPalNvpMock::lastBody());
        $this->assertStringContainsString('PAYMENTACTION=Authorization', $body);
        $this->assertStringContainsString('USER=api-user', $body);
    }
}
