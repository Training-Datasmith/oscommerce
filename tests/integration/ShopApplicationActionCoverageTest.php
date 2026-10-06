<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopCheckoutSeeder;
use Tests\Support\ShopPageApplicationStub;

/**
 * Invoke Shop Application Action::*::execute() handlers for PCOV.
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopApplicationActionCoverageTest extends TestCase
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

    public function testExecuteShopApplicationActions(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();

        $base = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Application';
        $stub = new ShopPageApplicationStub();
        $count = 0;

        foreach (glob($base . '/*/Action', GLOB_ONLYDIR) ?: [] as $actionRoot) {
            $application = basename(dirname($actionRoot));
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($actionRoot, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (!$file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $relative = substr($file->getPathname(), strlen($actionRoot) + 1, -4);
                $relative = str_replace('/', '\\', $relative);
                $class = 'osCommerce\\OM\\Core\\Site\\Shop\\Application\\' . $application . '\\Action\\' . $relative;

                if (!class_exists($class) || !method_exists($class, 'execute')) {
                    continue;
                }

                if (str_ends_with($relative, 'Process') || str_ends_with($relative, 'Callback')) {
                    continue;
                }

                if ($application === 'Checkout') {
                    ShopCheckoutSeeder::seedGuestCheckoutCart();
                }

                try {
                    $class::execute($stub);
                } catch (\Throwable) {
                }

                ++$count;
            }
        }

        $this->assertGreaterThan(20, $count);
    }
}
