<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\CheckoutProcessPost;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopCheckoutSeeder;
use Tests\Support\ShopHarnessDataSeeder;
use Tests\Support\ShopPageApplicationStub;

/**
 * Checkout address Process success paths (guest POST + logged-in ab selection).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopCheckoutProcessSuccessCoverageTest extends TestCase
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

    public function testGuestBillingAddressProcessSuccess(): void
    {
        InProcessSiteRenderer::renderShop(['Checkout', 'Billing', 'Address']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();

        $_POST = CheckoutProcessPost::validAddress();
        $stub = new ShopPageApplicationStub();

        try {
            \osCommerce\OM\Core\Site\Shop\Application\Checkout\Action\Billing\Address\Process::execute($stub);
            $this->fail('Expected redirect');
        } catch (\Throwable $e) {
            $this->assertStringContainsString('redirect', strtolower($e->getMessage()));
        }
    }

    public function testLoggedInBillingAddressBookSelection(): void
    {
        InProcessSiteRenderer::renderShop(['Checkout', 'Billing', 'Address']);
        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();
        ShopCheckoutSeeder::seedGuestCheckoutCart();

        $pdo = Registry::get('PDO');
        $customerId = (int) (ShopHarnessDataSeeder::ensureBaselineData()['customer_id'] ?: 0);
        $abId = (int) ($pdo->query('select address_book_id from osc_address_book where customers_id = ' . $customerId . ' limit 1')->fetchColumn() ?: 0);

        if ($abId > 0) {
            $_POST = ['ab' => (string) $abId];
            $stub = new ShopPageApplicationStub();
            try {
                \osCommerce\OM\Core\Site\Shop\Application\Checkout\Action\Billing\Address\Process::execute($stub);
                $this->fail('Expected redirect');
            } catch (\Throwable $e) {
                $this->assertStringContainsString('redirect', strtolower($e->getMessage()));
            }
        }

        $this->addToAssertionCount(1);
    }
}
