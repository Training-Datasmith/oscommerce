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
 * Order::insert prepOrderID edge paths and exists() filtering (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopOrderEdgeCoverageTest extends TestCase
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
        unset($_SESSION['prepOrderID']);
    }

    public function testPrepOrderIdMismatchRemovesPreparingOrder(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedGuestCheckoutCart();

        $cart = Registry::get('ShoppingCart');
        $firstCartId = $cart->getCartID();

        $orderId = Order::insert();
        $this->assertGreaterThan(0, $orderId);

        $_SESSION['prepOrderID'] = 'wrongcartid-' . $orderId;

        $cart->reset();
        ShopCheckoutSeeder::seedGuestCheckoutCart();
        $this->assertNotSame($firstCartId, $cart->getCartID());

        $newOrderId = Order::insert();
        $this->assertGreaterThan(0, $newOrderId);
        $this->assertNotSame($orderId, $newOrderId);
        $this->assertFalse((new Order($orderId))->exists($orderId));

        (new Order($newOrderId))->remove($newOrderId);
    }

    public function testExistsRequiresMatchingCustomerWhenProvided(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        $ids = ShopHarnessDataSeeder::ensureBaselineData();
        $orderId = $ids['order_id'];
        $customerId = $ids['customer_id'];

        $order = new Order($orderId);
        $this->assertTrue($order->exists($orderId, $customerId));
        $this->assertFalse($order->exists($orderId, $customerId + 99999));
    }

    public function testGetListingPaginationKeyword(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();

        $_GET['page'] = '1';
        $listing = Order::getListing(2, 'page');
        $this->assertIsArray($listing);

        unset($_GET['page']);
    }
}
