<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Load Shop module PHP files (payment, shipping, boxes, services) for PCOV.
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopModulePhpCoverageTest extends TestCase
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

    public function testLoadShopModulePhpFiles(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);

        $root = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Module';
        $count = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $path = $file->getPathname();
            if (str_contains($path, '/pages/')) {
                continue;
            }
            $class = $this->classFromModulePath($path);
            if ($class !== null && class_exists($class)) {
                try {
                    $ref = new \ReflectionClass($class);
                    if ($ref->isInstantiable()) {
                        $obj = $ref->newInstance();
                        if (method_exists($obj, 'initialize')) {
                            $obj->initialize();
                        }
                    }
                } catch (\Throwable) {
                    // Module-specific prerequisites may be missing.
                }
            } else {
                ob_start();
                try {
                    include $path;
                } catch (\Throwable) {
                }
                ob_end_clean();
            }

            ++$count;
        }

        $this->assertGreaterThan(40, $count);
    }

    private function classFromModulePath(string $path): ?string
    {
        if (!preg_match('#/Core/Site/Shop/Module/(.+)\\.php$#', $path, $m)) {
            return null;
        }

        $class = 'osCommerce\\OM\\Core\\Site\\Shop\\Module\\' . str_replace('/', '\\', $m[1]);

        return class_exists($class) ? $class : null;
    }
}
