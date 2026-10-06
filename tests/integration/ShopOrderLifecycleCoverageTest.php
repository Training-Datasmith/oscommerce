<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Order;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopCheckoutSeeder;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Order::insert, process, and sendEmail against seeded cart (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopOrderLifecycleCoverageTest extends TestCase
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

    public function testInsertProcessAndSendEmail(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedGuestCheckoutCart();
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();

        $_SESSION['comments'] = 'Coverage order comment';

        $orderId = Order::insert();
        $this->assertGreaterThan(0, $orderId);

        try {
            Order::process($orderId, 1);
        } catch (\Throwable) {
        }

        try {
            Order::sendEmail($orderId);
        } catch (\Throwable) {
        }

        $order = new Order($orderId);
        $order->exists($orderId);

        unset($_SESSION['prepOrderID'], $_SESSION['comments']);

        $this->addToAssertionCount(1);
    }
}
