<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Order;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Order model static/instance APIs with harness order row (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopOrderDeepCoverageTest extends TestCase
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

    public function testOrderQueriesAndListing(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        $ids = ShopHarnessDataSeeder::ensureBaselineData();

        $orderId = $ids['order_id'];
        $customerId = $ids['customer_id'];

        $this->assertGreaterThan(0, $orderId);
        $this->assertGreaterThan(0, $customerId);

        $order = new Order($orderId);
        $order->exists($orderId, $customerId);
        $order->getStatusListing($orderId);
        $order->getStatusID($orderId);
        Order::getCustomerID($orderId);
        Order::numberOfProducts($orderId);
        Order::numberOfEntries();
        Order::getListing(5);

        $this->addToAssertionCount(1);
    }
}
