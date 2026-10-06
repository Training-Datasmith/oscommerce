<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Include ini-style language files (plain text PHP) for line coverage.
 *
 * @group integration
 */
#[Group('integration')]
class LanguageIniPhpIncludeTest extends TestCase
{
    public function testIncludeAllLanguagePhpFiles(): void
    {
        $root = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site';
        $count = 0;

        foreach (['Admin', 'Shop', 'Setup'] as $site) {
            $dir = $root . '/' . $site . '/languages';
            if (!is_dir($dir)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (!$file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                ob_start();
                include $file->getPathname();
                ob_end_clean();
                ++$count;
            }
        }

        $this->assertGreaterThan(50, $count);
    }
}
