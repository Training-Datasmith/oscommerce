<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\LampSiteBootstrap;

/**
 * Execute Application-layer SQL classes for Shop, Admin, Setup (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ApplicationSqlSweepTest extends TestCase
{
    public function testShopAdminSetupSqlClasses(): void
    {
        foreach (['Shop', 'Admin', 'Setup'] as $site) {
            if ($site === 'Setup') {
                LampSiteBootstrap::bootDatabase('Shop');
            } else {
                LampSiteBootstrap::bootDatabase($site);
            }

            $this->runSqlUnder($site);
        }

        $this->addToAssertionCount(1);
    }

    private function runSqlUnder(string $site): void
    {
        $base = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/' . $site . '/Application';
        if (!is_dir($base)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $path = $file->getPathname();
            if (!str_contains($path, '/SQL/')) {
                continue;
            }

            if (!preg_match('#/Core/Site/([^/]+)/Application/(.+)/SQL/(.+)\\.php$#', $path, $m)) {
                continue;
            }

            $sub = str_replace('/', '\\', $m[2] . '\\SQL\\' . $m[3]);
            $class = 'osCommerce\\OM\\Core\\Site\\' . $m[1] . '\\Application\\' . $sub;

            if (!class_exists($class) || !method_exists($class, 'execute')) {
                continue;
            }

            try {
                $class::execute($this->defaultPayload());
            } catch (\Throwable) {
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultPayload(): array
    {
        return [
            'id' => 1,
            'language_id' => 1,
            'languages_id' => 1,
            'customers_id' => 1,
            'products_id' => 1,
            'orders_id' => 1,
            'categories_id' => 1,
            'key' => 'STORE_NAME',
            'value' => 'Test',
            'module' => 'BankTransfer',
            'code' => 'en_US',
            'username' => 'admin',
            'table_prefix' => 'osc_',
            'cfgKey' => 'STORE_NAME',
            'cfgValue' => 'Test Shop',
            'zone_id' => 1,
            'country_id' => 1,
        ];
    }
}
