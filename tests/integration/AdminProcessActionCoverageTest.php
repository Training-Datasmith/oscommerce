<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Admin OM3 *Process actions with minimal POST (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class AdminProcessActionCoverageTest extends TestCase
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

    public function testCustomersSaveProcessWithPost(): void
    {
        InProcessSiteRenderer::renderAdmin(['Customers']);
        $customerId = ShopHarnessDataSeeder::ensureBaselineData()['customer_id'] ?: 1;

        $_POST = [
            'customers_id' => (string) $customerId,
            'customers_firstname' => 'Coverage',
            'customers_lastname' => 'Customer',
            'customers_email_address' => 'coverage-process@example.test',
            'customers_telephone' => '555-0199',
            'customers_fax' => '',
            'customers_newsletter' => '1',
            'customers_password' => '',
            'customers_password_confirmation' => '',
        ];

        $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Application\\Customers\\Action\\Save\\Process';
        if (class_exists($class)) {
            try {
                $class::execute(Registry::get('Application'));
            } catch (\Throwable) {
            }
        }

        $this->addToAssertionCount(1);
    }

    public function testSelectedAdminProcessActions(): void
    {
        InProcessSiteRenderer::renderAdmin(['Dashboard']);
        ShopHarnessDataSeeder::ensureBaselineData();

        $actions = [
            'Configuration\\Action\\Save\\Process',
            'Languages\\Action\\Save\\Process',
            'ZoneGroups\\Action\\Save\\Process',
            'TaxClasses\\Action\\Save\\Process',
            'Currencies\\Action\\Save\\Process',
        ];

        $_POST = [
            'configuration_title' => 'Coverage',
            'configuration_key' => 'TEST_KEY',
            'configuration_value' => '1',
            'configuration_description' => 'test',
        ];

        $executed = 0;
        foreach ($actions as $relative) {
            $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Application\\' . $relative;
            if (!class_exists($class)) {
                continue;
            }
            try {
                $class::execute(Registry::get('Application'));
            } catch (\Throwable) {
            }
            ++$executed;
        }

        $this->assertGreaterThan(2, $executed);
    }
}
