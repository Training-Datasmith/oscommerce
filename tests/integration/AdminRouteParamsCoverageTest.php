<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Admin OM3 routes with typical editor query parameters (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class AdminRouteParamsCoverageTest extends TestCase
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

    public function testAdminEditorRoutes(): void
    {
        InProcessSiteRenderer::renderAdmin(['Dashboard']);
        $customerId = (string) (ShopHarnessDataSeeder::ensureBaselineData()['customer_id'] ?: 1);

        $matrix = [
            [['Customers'], [], ['customers_id' => $customerId]],
            [['Customers'], ['Edit'], ['customers_id' => $customerId]],
            [['Categories'], [], ['cID' => '0']],
            [['Languages'], [], ['languages_id' => '1']],
            [['Languages'], ['Edit'], ['languages_id' => '1']],
            [['Languages'], ['Definitions'], ['languages_id' => '1']],
            [['Languages'], ['Definitions', 'Edit'], ['languages_id' => '1', 'definition_id' => '1']],
            [['ZoneGroups'], [], ['zone_groups_id' => '1']],
            [['ZoneGroups'], ['EntriesEdit'], ['zone_groups_id' => '1', 'zone_id' => '1']],
            [['Countries'], [], ['countries_id' => '1']],
            [['Countries'], ['Zones'], ['countries_id' => '1']],
            [['Currencies'], [], ['currencies_id' => '1']],
            [['Currencies'], ['Edit'], ['currencies_id' => '1']],
            [['TaxClasses'], [], ['tax_class_id' => '1']],
            [['Administrators'], [], ['administrators_id' => '1']],
            [['PaymentModules'], ['Edit'], ['module' => 'BankTransfer']],
            [['Configuration'], [], ['group' => '1']],
            [['CreditCards'], ['Edit'], ['credit_cards_id' => '1']],
            [['Services'], [], []],
            [['ErrorLog'], [], []],
            [['CoreUpdate'], [], []],
        ];

        foreach ($matrix as [$parts, $actions, $params]) {
            $route = array_merge($parts, $actions);
            InProcessSiteRenderer::renderAdmin($route, $params);
        }

        $this->addToAssertionCount(1);
    }
}
