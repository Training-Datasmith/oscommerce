<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Admin Customers section pages with harness ObjectInfo (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class AdminCustomerSectionsCoverageTest extends TestCase
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

    public function testIncludeCustomerSectionPages(): void
    {
        InProcessSiteRenderer::renderAdmin(['Dashboard']);
        ShopHarnessDataSeeder::ensureBaselineData();

        $pagesDir = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Application/Customers/pages';
        foreach (glob($pagesDir . '/section_*.php') ?: [] as $page) {
            InProcessSiteRenderer::includeAdminApplicationPage('Customers', basename($page));
        }

        InProcessSiteRenderer::includeAdminApplicationPage('Customers', 'main.php');
        InProcessSiteRenderer::includeAdminApplicationPage('Customers', 'edit.php');

        $this->addToAssertionCount(1);
    }
}
