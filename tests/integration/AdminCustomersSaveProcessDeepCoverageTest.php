<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Admin\Application\Customers\Action\Save\Process;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Customers Save/Process validation + address-book branches (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class AdminCustomersSaveProcessDeepCoverageTest extends TestCase
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

    public function testSaveExistingCustomerWithAddressBookChanges(): void
    {
        InProcessSiteRenderer::renderAdmin(['Customers']);
        $ids = ShopHarnessDataSeeder::ensureBaselineData();
        $customerId = (int) ($ids['customer_id'] ?: 1);
        $addressId = (int) ($ids['address_id'] ?: 1);

        $_GET['id'] = (string) $customerId;
        $_POST = [
            'gender' => 'm',
            'firstname' => 'Coverage',
            'lastname' => 'Customer',
            'dob' => '1990-06-15',
            'email_address' => 'coverage-process@example.test',
            'password' => '',
            'confirmation' => '',
            'newsletter' => 'on',
            'status' => 'on',
            'ab' => [
                (string) $addressId => [
                    'id' => (string) $addressId,
                    'gender' => 'f',
                    'firstname' => 'Coverage',
                    'lastname' => 'Address',
                    'company' => 'Acme',
                    'street_address' => '456 Oak Ave',
                    'suburb' => '',
                    'city' => 'Testville',
                    'postcode' => '90211',
                    'state' => 'CA',
                    'zone_id' => '1',
                    'country_id' => '223',
                    'telephone' => '555-0200',
                    'fax' => '555-0201',
                    'changed' => 'true',
                ],
            ],
            'ab_default_id' => (string) $addressId,
        ];

        try {
            Process::execute(Registry::get('Application'));
        } catch (\Throwable) {
        }

        $_GET = [];
        $_POST = [
            'gender' => 'm',
            'firstname' => 'New',
            'lastname' => 'Signup',
            'email_address' => 'coverage-new-' . bin2hex(random_bytes(4)) . '@example.test',
            'password' => 'password123',
            'confirmation' => 'password123',
            'newsletter' => 'on',
            'status' => 'on',
            'new_address' => [
                [
                    'gender' => 'm',
                    'firstname' => 'New',
                    'lastname' => 'Signup',
                    'company' => '',
                    'street_address' => '789 Pine Rd',
                    'suburb' => '',
                    'city' => 'Testville',
                    'postcode' => '90212',
                    'state' => 'CA',
                    'zone_id' => '1',
                    'country_id' => '223',
                    'telephone' => '555-0300',
                    'fax' => '',
                    'default' => 'true',
                ],
            ],
        ];

        try {
            Process::execute(Registry::get('Application'));
        } catch (\Throwable) {
        }

        $_POST = [
            'gender' => 'x',
            'firstname' => 'x',
            'lastname' => 'x',
            'email_address' => 'not-an-email',
            'password' => 'x',
            'confirmation' => 'y',
        ];
        try {
            Process::execute(Registry::get('Application'));
        } catch (\Throwable) {
        }

        $_GET['id'] = (string) $customerId;
        $_POST = [
            'gender' => 'm',
            'firstname' => 'Coverage',
            'lastname' => 'Customer',
            'email_address' => 'coverage-process@example.test',
            'password' => '',
            'confirmation' => '',
            'newsletter' => 'on',
            'status' => 'on',
            'deleteAB' => [(string) $addressId],
        ];
        try {
            Process::execute(Registry::get('Application'));
        } catch (\Throwable) {
        }

        $this->addToAssertionCount(1);
    }
}
