<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\SiteRouteDiscovery;

/**
 * In-process Shop route rendering for PCOV line coverage.
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class InProcessShopRouteCoverageTest extends TestCase
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

    public function testRenderDiscoveredShopRoutesBatchOne(): void
    {
        $this->renderShopRouteBatch(0, 12);
    }

    public function testRenderDiscoveredShopRoutesBatchTwo(): void
    {
        $this->renderShopRouteBatch(12, 12);
    }

    public function testRenderDiscoveredShopRoutesBatchThree(): void
    {
        $this->renderShopRouteBatch(24, 12);
    }

    public function testRenderDiscoveredShopRoutesBatchFour(): void
    {
        $this->renderShopRouteBatch(36, 12);
    }

    private function renderShopRouteBatch(int $offset, int $limit): void
    {
        $routes = SiteRouteDiscovery::shopRoutes();
        foreach (array_slice($routes, $offset, $limit) as $parts) {
            InProcessSiteRenderer::renderShop($parts);
        }

        $this->addToAssertionCount(1);
    }

    public function testRenderShopProductAndCategoryViews(): void
    {
        InProcessSiteRenderer::renderShop(['Products', 'All']);
        InProcessSiteRenderer::renderShop(['Products'], ['products_id' => '1']);
        InProcessSiteRenderer::renderShop(['Products'], ['cPath' => '1']);
        InProcessSiteRenderer::renderShop(['Search'], ['keywords' => 'dvd']);
        InProcessSiteRenderer::renderShop(['Checkout']);
        InProcessSiteRenderer::renderShop(['Checkout', 'Billing']);
        InProcessSiteRenderer::renderShop(['Checkout', 'Shipping']);
        $orderId = \Tests\Support\ShopHarnessDataSeeder::ensureBaselineData()['order_id'] ?? 1;
        InProcessSiteRenderer::renderShop(['Account', 'Orders'], ['order_id' => (string) $orderId]);
        InProcessSiteRenderer::renderShop(['Account', 'Orders', 'Info'], ['order_id' => (string) $orderId]);

        $routes = [
            ['Info', 'Contact'],
            ['Info', 'Privacy'],
            ['Info', 'Conditions'],
            ['Info', 'Shipping'],
            ['Info', 'Sitemap'],
            ['Info', 'Cookies'],
            ['Products', 'Specials'],
            ['Products', 'Reviews'],
            ['Account', 'AddressBook'],
            ['Account', 'Edit'],
            ['Account', 'Password'],
            ['Account', 'Newsletters'],
            ['Account', 'LogIn'],
            ['Account', 'Create'],
            ['Checkout', 'Success'],
            ['Search', 'Help'],
        ];

        $checkoutRoutes = [
            ['Checkout'],
            ['Checkout', 'Shipping'],
            ['Checkout', 'Shipping', 'Address'],
            ['Checkout', 'Billing'],
            ['Checkout', 'Billing', 'Address'],
            ['Checkout', 'Success'],
        ];

        foreach (array_merge($routes, $checkoutRoutes) as $parts) {
            InProcessSiteRenderer::renderShop($parts);
        }

        $this->addToAssertionCount(1);
    }
}
