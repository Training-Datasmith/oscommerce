<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopCheckoutSeeder;

/**
 * oscom.php HPDL legacy template branches (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopOscomLegacyLayoutCoverageTest extends TestCase
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

    public function testLegacyHpdlOscomBranches(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();

        foreach (['main.php', 'product_listing.php'] as $page) {
            InProcessSiteRenderer::includeShopOscomLegacyLayout('Index', $page);
            InProcessSiteRenderer::includeShopOscomLegacyLayout('Products', $page);
        }

        InProcessSiteRenderer::includeShopOscomLegacyLayout('Checkout', 'billing.php');

        $this->addToAssertionCount(1);
    }
}
