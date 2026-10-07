<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Admin\Application\Customers\Action\Save\Process;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\AdminCustomerProcessPost;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Customers Save/Process happy path through save + saveAddress (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class AdminCustomersSaveProcessSuccessCoverageTest extends TestCase
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

    public function testUpdateCustomerAndAddressBook(): void
    {
        InProcessSiteRenderer::renderAdmin(['Customers']);
        $ids = ShopHarnessDataSeeder::ensureBaselineData();
        $customerId = (int) ($ids['customer_id'] ?: 1);
        $addressId = (int) ($ids['address_id'] ?: 1);

        $pdo = Registry::get('PDO');
        $row = $pdo->query(
            'select customers_firstname, customers_lastname, customers_email_address from osc_customers where customers_id = ' . $customerId
        )->fetch(\PDO::FETCH_ASSOC) ?: [];

        $_GET['id'] = (string) $customerId;
        $_POST = AdminCustomerProcessPost::customerFields(
            (string) ($row['customers_firstname'] ?? 'Coverage'),
            (string) ($row['customers_lastname'] ?? 'Customer'),
            (string) ($row['customers_email_address'] ?? 'coverage-customer@example.test'),
        );
        $_POST['ab'] = [(string) $addressId => AdminCustomerProcessPost::addressFields($addressId, true)];
        $_POST['ab_default_id'] = (string) $addressId;

        $this->runProcessExpectingRedirect();

        $_GET['id'] = (string) $customerId;
        $_POST = AdminCustomerProcessPost::customerFields(
            (string) ($row['customers_firstname'] ?? 'Coverage'),
            (string) ($row['customers_lastname'] ?? 'Customer'),
            (string) ($row['customers_email_address'] ?? 'coverage-customer@example.test'),
        );
        $_POST['new_address'] = [
            array_merge(AdminCustomerProcessPost::addressFields(0, false), [
                'default' => 'false',
                'firstname' => 'Added',
                'lastname' => 'ViaProcess',
            ]),
        ];
        unset($_POST['new_address'][0]['id'], $_POST['new_address'][0]['changed']);

        $this->runProcessExpectingRedirect();

        $this->addToAssertionCount(1);
    }

    private function runProcessExpectingRedirect(): void
    {
        try {
            Process::execute(Registry::get('Application'));
            $this->addToAssertionCount(1);
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'OSCOM redirect')) {
                $this->addToAssertionCount(1);

                return;
            }
            $this->fail('Process::execute threw: ' . $e->getMessage());
        }
    }
}
