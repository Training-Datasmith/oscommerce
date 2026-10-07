<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Application\Checkout\Action\Billing\Address\Process as BillingAddressProcess;
use osCommerce\OM\Core\Site\Shop\Application\Checkout\Action\Shipping\Address\Process as ShippingAddressProcess;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopCheckoutSeeder;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Products main/reviews templates and Checkout address Process actions (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopProductsCheckoutAddressCoverageTest extends TestCase
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
        unset($_POST);
    }

    public function testProductsApplicationPagesWithProductScope(): void
    {
        InProcessSiteRenderer::renderShop(['Products']);
        ShopHarnessDataSeeder::ensureBaselineData();

        $pdo = Registry::get('PDO');
        $variantMaster = (int) ($pdo->query(
            'select products_id from osc_products where products_status = 1 and has_children = 1 limit 1'
        )->fetchColumn() ?: 0);
        if ($variantMaster > 0) {
            $_GET['products_id'] = (string) $variantMaster;
        }

        foreach (
            [
                'main.php',
                'reviews_product.php',
                'reviews_view.php',
                'reviews_write.php',
                'tell_a_friend.php',
            ] as $page
        ) {
            InProcessSiteRenderer::includeShopApplicationPage('Products', $page);
            InProcessSiteRenderer::includeShopPageViaOscomLayout('Products', $page);
        }

        $this->addToAssertionCount(1);
    }

    public function testCheckoutShippingAddressProcessGuestAndLoggedIn(): void
    {
        InProcessSiteRenderer::renderShop(['Checkout', 'Shipping', 'Address']);
        ShopCheckoutSeeder::seedGuestCheckoutCart(false);
        ShopCheckoutSeeder::ensureShippingWithQuotes(false);

        $address = ShopCheckoutSeeder::sampleAddress();
        $_POST = [
            'gender' => $address['gender'],
            'firstname' => $address['firstname'],
            'lastname' => $address['lastname'],
            'company' => $address['company'],
            'street_address' => $address['street_address'],
            'suburb' => $address['suburb'],
            'city' => $address['city'],
            'postcode' => $address['postcode'],
            'state' => 'CA',
            'country' => (string) $address['country_id'],
            'telephone' => $address['telephone'],
            'fax' => $address['fax'],
        ];

        try {
            ShippingAddressProcess::execute(Registry::get('Application'));
        } catch (\Throwable $e) {
            if (!str_contains($e->getMessage(), 'OSCOM redirect')) {
                throw $e;
            }
        }

        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();

        $pdo = Registry::get('PDO');
        $customerId = (int) Registry::get('Customer')->getID();
        $abId = (int) ($pdo->query(
            'select address_book_id from osc_address_book where customers_id = ' . $customerId . ' limit 1'
        )->fetchColumn() ?: 0);

        if ($abId > 0) {
            $_POST = ['ab' => (string) $abId];
            try {
                ShippingAddressProcess::execute(Registry::get('Application'));
            } catch (\Throwable $e) {
                if (!str_contains($e->getMessage(), 'OSCOM redirect')) {
                    throw $e;
                }
            }
        }

        $_POST = ['firstname' => 'x'];
        try {
            ShippingAddressProcess::execute(Registry::get('Application'));
        } catch (\Throwable $e) {
            if (!str_contains($e->getMessage(), 'OSCOM redirect')) {
                throw $e;
            }
        }

        $this->addToAssertionCount(1);
    }

    public function testCheckoutBillingAddressProcess(): void
    {
        InProcessSiteRenderer::renderShop(['Checkout', 'Billing', 'Address']);
        ShopCheckoutSeeder::seedGuestCheckoutCart(false);

        $address = ShopCheckoutSeeder::sampleAddress();
        $_POST = [
            'gender' => $address['gender'],
            'firstname' => $address['firstname'],
            'lastname' => $address['lastname'],
            'street_address' => $address['street_address'],
            'city' => $address['city'],
            'postcode' => $address['postcode'],
            'state' => 'CA',
            'country' => (string) $address['country_id'],
            'telephone' => $address['telephone'],
        ];

        try {
            BillingAddressProcess::execute(Registry::get('Application'));
        } catch (\Throwable $e) {
            if (!str_contains($e->getMessage(), 'OSCOM redirect')) {
                throw $e;
            }
        }

        InProcessSiteRenderer::includeShopApplicationPage('Checkout', 'main.php');
        InProcessSiteRenderer::includeShopApplicationPage('Checkout', 'billing.php');

        $this->addToAssertionCount(1);
    }
}
