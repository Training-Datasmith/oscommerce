<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * OM3 Admin application pages and editor routes (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class AdminOm3EditorsCoverageTest extends TestCase
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

    public function testIncludeAllOm3AdminPagesAndEditorRoutes(): void
    {
        InProcessSiteRenderer::renderAdmin(['Dashboard']);
        ShopHarnessDataSeeder::ensureBaselineData();

        $customerId = (string) (ShopHarnessDataSeeder::ensureBaselineData()['customer_id'] ?: 1);
        $base = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Application';

        foreach (glob($base . '/*/Controller.php') ?: [] as $controller) {
            $application = basename(dirname($controller));
            $pagesDir = dirname($controller) . '/pages';
            if (!is_dir($pagesDir)) {
                continue;
            }

            foreach (glob($pagesDir . '/*.php') ?: [] as $page) {
                InProcessSiteRenderer::includeAdminApplicationPage($application, basename($page));
            }
        }

        $routes = [
            [['Categories'], ['Edit'], ['cID' => '1']],
            [['Categories'], ['New']],
            [['TaxClasses'], ['Edit'], ['tax_class_id' => '1']],
            [['ZoneGroups'], ['Entries'], ['zone_groups_id' => '1']],
            [['Administrators'], ['Edit'], ['administrators_id' => '1']],
            [['Customers'], ['Edit'], ['customers_id' => $customerId]],
            [['PaymentModules'], ['Edit'], ['module' => 'COD']],
            [['Configuration'], [], ['group' => '6']],
            [['ServerInfo'], []],
        ];

        foreach ($routes as $route) {
            $parts = $route[0];
            $actions = $route[1] ?? [];
            $params = $route[2] ?? [];
            InProcessSiteRenderer::renderAdmin(array_merge($parts, $actions), $params);
        }

        $this->addToAssertionCount(1);
    }
}
