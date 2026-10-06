<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Direct include of every Shop application page/*.php for PCOV.
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopAllPagesCoverageTest extends TestCase
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

    public function testIncludeAllShopApplicationPages(): void
    {
        $base = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Application';
        $count = 0;

        foreach (glob($base . '/*/pages/*.php') ?: [] as $page) {
            $application = basename(dirname(dirname($page)));
            $filename = basename($page);
            InProcessSiteRenderer::includeShopApplicationPage($application, $filename);
            ++$count;
        }

        InProcessSiteRenderer::includeShopModulePages();

        $this->assertGreaterThan(45, $count);
    }
}
