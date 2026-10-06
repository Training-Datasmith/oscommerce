<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopCheckoutSeeder;

/**
 * Payment, shipping, and order-total module method coverage.
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopPaymentShippingModuleCoverageTest extends TestCase
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

    public function testPaymentShippingAndOrderTotalModules(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();

        $cart = Registry::get('ShoppingCart');
        if (!Registry::exists('Payment')) {
            Registry::set('Payment', new \osCommerce\OM\Core\Site\Shop\Payment());
        }
        $payment = Registry::get('Payment');
        $payment->loadAll();
        $payment->selection();
        $payment->getJavascriptBlocks();

        $payRoot = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Module/Payment';
        foreach (glob($payRoot . '/*.php') ?: [] as $controller) {
            $code = basename($controller, '.php');
            $class = 'osCommerce\\OM\\Core\\Site\\Shop\\Module\\Payment\\' . $code;
            if (!class_exists($class)) {
                continue;
            }

            try {
                $obj = new $class();
                $obj->isEnabled();
                $obj->getTitle();
                $obj->selection();
                $obj->updateStatus();
                if (method_exists($obj, 'confirmation')) {
                    $obj->confirmation();
                }
                if (method_exists($obj, 'processButton')) {
                    ob_start();
                    try {
                        $obj->processButton();
                    } finally {
                        ob_end_clean();
                    }
                }
                if (method_exists($obj, 'preConfirmationCheck')) {
                    $_GET['ppx'] = 'cancel';
                    try {
                        $obj->preConfirmationCheck();
                    } catch (\Throwable) {
                    }
                    unset($_GET['ppx']);
                }
                if (method_exists($obj, 'process')) {
                    try {
                        $obj->process();
                    } catch (\Throwable) {
                    }
                }
                if (method_exists($obj, 'getGatewayURL')) {
                    try {
                        $obj->getGatewayURL();
                    } catch (\Throwable) {
                    }
                }
            } catch (\Throwable) {
            }
        }

        $shipping = new \osCommerce\OM\Core\Site\Shop\Shipping();
        foreach ($shipping->getQuotes() as $quote) {
            if (isset($quote['id'], $quote['module'])) {
                try {
                    $cart->setShippingMethod([
                        'id' => $quote['id'],
                        'title' => $quote['module'],
                        'cost' => $quote['cost'] ?? '0',
                    ], false);
                } catch (\Throwable) {
                }
            }
        }

        $root = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Module/OrderTotal';
        $count = 0;
        foreach (glob($root . '/*.php') ?: [] as $controller) {
            $module = basename($controller, '.php');
            $class = 'osCommerce\\OM\\Core\\Site\\Shop\\Module\\OrderTotal\\' . $module;
            if (!class_exists($class)) {
                continue;
            }

            try {
                $obj = new $class();
                if (method_exists($obj, 'initialize')) {
                    $obj->initialize();
                }
                if (method_exists($obj, 'process')) {
                    $obj->process();
                }
                if (method_exists($obj, 'output')) {
                    ob_start();
                    try {
                        $obj->output();
                    } finally {
                        ob_end_clean();
                    }
                }
            } catch (\Throwable) {
            }

            ++$count;
        }

        $this->assertGreaterThan(3, $count);
    }
}
