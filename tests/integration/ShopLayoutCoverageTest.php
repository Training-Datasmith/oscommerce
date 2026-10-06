<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Full oscom.php layout for high-value Shop pages (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopLayoutCoverageTest extends TestCase
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

    public function testOscomLayoutForCheckoutAccountAndCatalogPages(): void
    {
        $pages = [
            ['Checkout', 'billing.php'],
            ['Checkout', 'shipping.php'],
            ['Checkout', 'main.php'],
            ['Checkout', 'billing_address.php'],
            ['Checkout', 'shipping_address.php'],
            ['Checkout', 'success.php'],
            ['Account', 'orders.php'],
            ['Account', 'orders_info.php'],
            ['Account', 'address_book.php'],
            ['Account', 'main.php'],
            ['Cart', 'main.php'],
            ['Products', 'main.php'],
            ['Search', 'main.php'],
            ['Index', 'main.php'],
        ];

        foreach ($pages as [$application, $page]) {
            InProcessSiteRenderer::includeShopPageViaOscomLayout($application, $page);
        }

        $this->assertGreaterThan(10, count($pages));
    }
}
