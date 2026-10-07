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
 * Exercise Shop templates/oscom.php via many routed pages (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopOscomTemplateCoverageTest extends TestCase
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

    public function testRenderRoutesThatLoadOscomLayout(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedGuestCheckoutCart();

        $routes = [
            ['Index'],
            ['Cart'],
            ['Checkout'],
            ['Checkout', 'Billing'],
            ['Checkout', 'Shipping'],
            ['Products'],
            ['Products', 'All'],
            ['Search'],
            ['Info', 'Contact'],
            ['Account'],
            ['Account', 'Orders'],
        ];

        foreach ($routes as $parts) {
            InProcessSiteRenderer::renderShop($parts);
        }

        $this->assertGreaterThan(5, count($routes));
    }
}
