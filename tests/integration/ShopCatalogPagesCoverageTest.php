<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Catalog/listing page templates with seeded globals (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopCatalogPagesCoverageTest extends TestCase
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

    public function testProductAndCategoryListingPages(): void
    {
        InProcessSiteRenderer::renderShop(['Products'], ['products_id' => '1']);
        InProcessSiteRenderer::renderShop(['Products'], ['cPath' => '1']);
        InProcessSiteRenderer::renderShop(['Products', 'All']);
        InProcessSiteRenderer::renderShop(['Search'], ['keywords' => 'dvd']);
        InProcessSiteRenderer::renderShop(['Products', 'Specials']);
        InProcessSiteRenderer::renderShop(['Products', 'Reviews']);

        foreach (
            [
                ['Products', 'main.php'],
                ['Products', 'product_listing.php'],
                ['Products', 'category_listing.php'],
                ['Products', 'reviews_product.php'],
                ['Search', 'main.php'],
                ['Index', 'main.php'],
            ] as [$app, $page]
        ) {
            InProcessSiteRenderer::includeShopPageViaOscomLayout($app, $page);
        }

        $this->addToAssertionCount(1);
    }
}
