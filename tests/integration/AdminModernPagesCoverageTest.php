<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Include Admin OM3 application pages (apps with Controller.php).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class AdminModernPagesCoverageTest extends TestCase
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

    public function testIncludeModernAdminPages(): void
    {
        $base = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Application';
        $count = 0;

        foreach (glob($base . '/*/Controller.php') ?: [] as $controller) {
            $application = basename(dirname($controller));
            $pagesDir = dirname($controller) . '/pages';
            if (!is_dir($pagesDir)) {
                continue;
            }

            foreach (glob($pagesDir . '/*.php') ?: [] as $page) {
                InProcessSiteRenderer::includeAdminApplicationPage($application, basename($page));
                ++$count;
            }
        }

        $this->assertGreaterThan(40, $count);
    }
}
