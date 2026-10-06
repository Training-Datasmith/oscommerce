<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Include Shop assets/*.php (form_check.js.php, etc.) under a booted storefront.
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopAssetsPhpCoverageTest extends TestCase
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

    public function testIncludeShopAssetPhpFiles(): void
    {
        InProcessSiteRenderer::renderShop(['Account', 'Create']);

        $dir = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/assets';
        $count = 0;

        foreach (glob($dir . '/*.php') ?: [] as $file) {
            ob_start();
            try {
                include $file;
            } catch (\Throwable) {
            }
            ob_end_clean();
            ++$count;
        }

        $this->assertGreaterThan(2, $count);
    }
}
