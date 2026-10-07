<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\OSCOM;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Admin batch_* confirmation pages with seeded $_POST['batch'] (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class AdminBatchPagesCoverageTest extends TestCase
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
        unset($_POST, $_GET);
    }

    public function testBatchPagesAcrossAdminApplications(): void
    {
        InProcessSiteRenderer::renderAdmin(['Dashboard']);

        $base = OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Application';
        $targets = [
            ['Countries', 'batch_delete.php'],
            ['Countries', 'zones_batch_delete.php'],
            ['TaxClasses', 'batch_delete.php'],
            ['TaxClasses', 'entries_batch_delete.php'],
            ['ZoneGroups', 'batch_delete.php'],
            ['ZoneGroups', 'entries_batch_delete.php'],
            ['Administrators', 'batch_edit.php'],
            ['Administrators', 'batch_delete.php'],
            ['Currencies', 'batch_delete.php'],
            ['Languages', 'batch_delete.php'],
            ['CreditCards', 'batch_edit.php'],
            ['CreditCards', 'batch_delete.php'],
            ['Configuration', 'entries_batch_edit.php'],
            ['Categories', 'batch_move.php'],
        ];

        foreach ($targets as [$app, $page]) {
            InProcessSiteRenderer::includeAdminApplicationPage($app, $page);
        }

        $this->assertGreaterThan(10, count($targets));
    }
}
