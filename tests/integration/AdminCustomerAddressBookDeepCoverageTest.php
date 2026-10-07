<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Customers section_addressBook with new and existing customer (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class AdminCustomerAddressBookDeepCoverageTest extends TestCase
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

    public function testSectionAddressBookNewAndExistingCustomer(): void
    {
        InProcessSiteRenderer::renderAdmin(['Customers']);
        $customerId = (string) (ShopHarnessDataSeeder::ensureBaselineData()['customer_id'] ?: 1);

        global $new_customer;
        $new_customer = true;
        $_GET['id'] = '0';
        InProcessSiteRenderer::includeAdminApplicationPage('Customers', 'section_addressBook.php');

        $new_customer = false;
        $_GET['id'] = $customerId;
        InProcessSiteRenderer::includeAdminApplicationPage('Customers', 'section_addressBook.php');

        foreach (glob(\osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Application/Customers/pages/section_*.php') ?: [] as $page) {
            InProcessSiteRenderer::includeAdminApplicationPage('Customers', basename($page));
        }

        $this->addToAssertionCount(1);
    }
}
