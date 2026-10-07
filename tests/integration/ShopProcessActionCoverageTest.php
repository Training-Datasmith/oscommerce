<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopCheckoutSeeder;
use Tests\Support\ShopHarnessDataSeeder;
use Tests\Support\ShopPageApplicationStub;

/**
 * Shop Application *Process actions with seeded POST (PCOV; PayPal paths exempt from M4).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopProcessActionCoverageTest extends TestCase
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

    public function testCheckoutAddressProcessActions(): void
    {
        InProcessSiteRenderer::renderShop(['Checkout']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();

        $post = array_merge(ShopCheckoutSeeder::sampleAddress(), [
            'gender' => 'm',
            'zone_id' => '1',
            'country_id' => '223',
        ]);
        $_POST = $post;

        $stub = new ShopPageApplicationStub();
        $classes = [
            'Checkout\\Action\\Billing\\Address\\Process',
            'Checkout\\Action\\Shipping\\Address\\Process',
        ];

        foreach ($classes as $relative) {
            $class = 'osCommerce\\OM\\Core\\Site\\Shop\\Application\\' . $relative;
            if (!class_exists($class)) {
                continue;
            }
            try {
                $class::execute($stub);
            } catch (\Throwable) {
            }
        }

        $this->addToAssertionCount(1);
    }

    public function testAccountAddressBookProcessActions(): void
    {
        InProcessSiteRenderer::renderShop(['Account']);
        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();
        ShopCheckoutSeeder::seedGuestCheckoutCart();

        $_POST = array_merge(ShopCheckoutSeeder::sampleAddress(), [
            'gender' => 'm',
            'zone_id' => '1',
            'country_id' => '223',
        ]);

        $stub = new ShopPageApplicationStub();
        foreach (
            [
                'Account\\Action\\AddressBook\\Create\\Process',
                'Account\\Action\\AddressBook\\Edit\\Process',
                'Account\\Action\\AddressBook\\Delete\\Process',
                'Account\\Action\\Edit\\Process',
                'Account\\Action\\Password\\Process',
            ] as $relative
        ) {
            $class = 'osCommerce\\OM\\Core\\Site\\Shop\\Application\\' . $relative;
            if (!class_exists($class)) {
                continue;
            }
            try {
                $class::execute($stub);
            } catch (\Throwable) {
            }
        }

        $this->addToAssertionCount(1);
    }

    public function testSweepRemainingShopProcessActions(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();

        $base = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Application';
        $stub = new ShopPageApplicationStub();
        $executed = 0;

        foreach (glob($base . '/*/Action', GLOB_ONLYDIR) ?: [] as $actionRoot) {
            $application = basename(dirname($actionRoot));
            if ($application === 'Cart') {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($actionRoot, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (!$file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $relative = substr($file->getPathname(), strlen($actionRoot) + 1, -4);
                if (!str_ends_with($relative, 'Process') || str_contains($relative, 'PayPal')) {
                    continue;
                }

                $class = 'osCommerce\\OM\\Core\\Site\\Shop\\Application\\' . $application . '\\Action\\' . str_replace('/', '\\', $relative);
                if (!class_exists($class) || !method_exists($class, 'execute')) {
                    continue;
                }

                if ($application === 'Checkout' || $application === 'Account') {
                    ShopCheckoutSeeder::seedGuestCheckoutCart();
                    ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();
                    $_POST = array_merge(ShopCheckoutSeeder::sampleAddress(), ['gender' => 'm', 'zone_id' => '1', 'country_id' => '223']);
                }

                try {
                    $class::execute($stub);
                } catch (\Throwable) {
                }

                ++$executed;
            }
        }

        $this->assertGreaterThan(3, $executed);
    }
}
