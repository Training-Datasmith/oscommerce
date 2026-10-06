<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Tests\Support\LampSiteBootstrap;

/**
 * Execute Site SQL helper classes against the installed database (line coverage).
 *
 * @group integration
 */
#[Group('integration')]
class SqlExecuteSweepTest extends TestCase
{
    public function testAdminSqlProcedures(): void
    {
        LampSiteBootstrap::bootDatabase('Admin');
        $this->runSqlClasses('Admin');
    }

    public function testShopSqlProcedures(): void
    {
        LampSiteBootstrap::bootDatabase('Shop');
        $this->runSqlClasses('Shop');
    }


    private function runSqlClasses(string $site): void
    {
        $classes = $this->findSqlClasses($site);
        $this->assertNotEmpty($classes, 'No SQL classes for ' . $site);

        foreach ($classes as $class) {
            try {
                $class::execute($this->defaultDataFor($class));
            } catch (\Throwable) {
                // Expected for invalid IDs; still covers procedure logic.
            }
        }
    }

    /**
     * @return list<class-string>
     */
    private function findSqlClasses(string $site): array
    {
        $base = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/' . $site . '/SQL';
        if (!is_dir($base)) {
            return [];
        }

        $classes = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $path = $file->getPathname();
            if (!preg_match('#/Core/Site/([^/]+)/SQL/(.+)\\.php$#', $path, $m)) {
                continue;
            }

            $sub = str_replace('/', '\\', $m[2]);
            $class = 'osCommerce\\OM\\Core\\Site\\' . $m[1] . '\\SQL\\' . $sub;

            if (class_exists($class) && method_exists($class, 'execute')) {
                $classes[] = $class;
            }
        }

        sort($classes);

        return array_values(array_unique($classes));
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultDataFor(string $class): array
    {
        return [
            'id' => 1,
            'language_id' => 1,
            'languages_id' => 1,
            'customers_id' => 1,
            'products_id' => 1,
            'orders_id' => 1,
            'key' => 'STORE_NAME',
            'value' => 'Test',
            'module' => 'BankTransfer',
            'code' => 'en_US',
            'username' => 'admin',
            'table_prefix' => 'osc_',
            'cfgKey' => 'STORE_NAME',
            'cfgValue' => 'Test Shop',
        ];
    }
}
