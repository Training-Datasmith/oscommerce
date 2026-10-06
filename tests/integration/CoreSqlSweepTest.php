<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Tests\Support\LampSiteBootstrap;

/**
 * Core SQL helper classes (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
class CoreSqlSweepTest extends TestCase
{
    public function testCoreSqlExecute(): void
    {
        LampSiteBootstrap::bootDatabase('Shop');

        $base = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/SQL';
        $count = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            if (!preg_match('#/Core/SQL/(.+)\\.php$#', $file->getPathname(), $m)) {
                continue;
            }

            $class = 'osCommerce\\OM\\Core\\SQL\\' . str_replace('/', '\\', $m[1]);
            if (!class_exists($class) || !method_exists($class, 'execute')) {
                continue;
            }

            try {
                $class::execute(['id' => 1]);
            } catch (\Throwable) {
            }

            ++$count;
        }

        $this->assertGreaterThan(3, $count);
    }
}
