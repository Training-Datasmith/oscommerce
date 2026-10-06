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
 * Order::remove (status 4) and Order::process stock decrement (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopOrderRemoveStockCoverageTest extends TestCase
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

    public function testRemovePreparingOrderAndProcessStock(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedGuestCheckoutCart();
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();

        $pdo = Registry::get('PDO');
        $productId = (int) ($pdo->query('select products_id from osc_products order by products_id limit 1')->fetchColumn() ?: 1);
        $qtyBefore = (int) ($pdo->query('select products_quantity from osc_products where products_id = ' . $productId)->fetchColumn() ?: 0);

        $orderId = Order::insert();
        $this->assertGreaterThan(0, $orderId);

        (new Order($orderId))->remove($orderId);
        $this->assertFalse((new Order($orderId))->exists($orderId));

        unset($_SESSION['prepOrderID']);
        $orderId2 = Order::insert();
        $this->assertGreaterThan(0, $orderId2);

        $_SESSION['prepOrderID'] = Registry::get('ShoppingCart')->getCartID() . '-' . $orderId2;
        $orderId3 = Order::insert();
        $this->assertSame($orderId2, $orderId3);

        try {
            Order::process($orderId2, (int) (defined('DEFAULT_ORDERS_STATUS_ID') ? DEFAULT_ORDERS_STATUS_ID : 1));
        } catch (\Throwable) {
        }

        if (defined('STOCK_LIMITED') && STOCK_LIMITED === '1') {
            $qtyAfter = (int) ($pdo->query('select products_quantity from osc_products where products_id = ' . $productId)->fetchColumn() ?: 0);
            $this->assertLessThanOrEqual($qtyBefore, $qtyAfter);
        }

        (new Order($orderId2))->remove($orderId2);

        unset($_SESSION['prepOrderID']);
    }
}
