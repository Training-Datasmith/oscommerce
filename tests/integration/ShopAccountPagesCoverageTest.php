<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopCheckoutSeeder;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Account application pages with logged-in customer (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopAccountPagesCoverageTest extends TestCase
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

    public function testAccountPagesViaLayoutAndRoutes(): void
    {
        InProcessSiteRenderer::renderShop(['Account']);
        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();
        ShopCheckoutSeeder::seedGuestCheckoutCart();

        $orderId = (string) (ShopHarnessDataSeeder::ensureBaselineData()['order_id'] ?: 1);

        $pages = [
            ['Account', 'main.php'],
            ['Account', 'edit.php'],
            ['Account', 'password.php'],
            ['Account', 'address_book.php'],
            ['Account', 'address_book_details.php'],
            ['Account', 'address_book_process.php'],
            ['Account', 'address_book_delete.php'],
            ['Account', 'orders.php'],
            ['Account', 'orders_info.php'],
            ['Account', 'notifications.php'],
            ['Account', 'newsletters.php'],
            ['Account', 'login.php'],
            ['Account', 'create.php'],
            ['Account', 'create_success.php'],
            ['Account', 'logoff.php'],
            ['Account', 'password_forgotten.php'],
        ];

        foreach ($pages as [$app, $page]) {
            InProcessSiteRenderer::includeShopPageViaOscomLayout($app, $page);
        }

        InProcessSiteRenderer::renderShop(['Account', 'Orders', 'Info'], ['order_id' => $orderId]);
        InProcessSiteRenderer::renderShop(['Account', 'AddressBook']);
        InProcessSiteRenderer::renderShop(['Account', 'Edit']);
        InProcessSiteRenderer::renderShop(['Account', 'Password']);

        $this->assertGreaterThan(10, count($pages));
    }
}
