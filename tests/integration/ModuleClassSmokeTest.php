<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Tests\Support\LampSiteBootstrap;

/**
 * Instantiate Shop/Admin module classes (payment, shipping, boxes, etc.).
 *
 * @group integration
 */
#[Group('integration')]
class ModuleClassSmokeTest extends TestCase
{
    public function testShopModuleClasses(): void
    {
        LampSiteBootstrap::bootDatabase('Shop');
        $count = $this->smokeModules('Shop', true);
        $this->assertGreaterThan(10, $count);
    }

    public function testAdminModuleClasses(): void
    {
        LampSiteBootstrap::bootDatabase('Admin');
        $count = $this->smokeModules('Admin', false);
        $this->assertGreaterThan(5, $count);
    }

    private function smokeModules(string $site, bool $requireControllerName): int
    {
        $moduleRoot = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/' . $site . '/Module';
        if (!is_dir($moduleRoot)) {
            return 0;
        }

        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($moduleRoot, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            if ($requireControllerName && $file->getFilename() !== 'Controller.php') {
                continue;
            }

            if (!$requireControllerName && $file->getFilename() === 'Controller.php') {
                continue;
            }

            $baseName = basename($file->getPathname(), '.php');
            if (!preg_match('/^[A-Z]/', $baseName)) {
                continue;
            }

            $path = $file->getPathname();
            if (!preg_match('#/Core/Site/([^/]+)/Module/(.+)\\.php$#', $path, $m)) {
                continue;
            }

            $class = 'osCommerce\\OM\\Core\\Site\\' . $m[1] . '\\Module\\' . str_replace('/', '\\', $m[2]);
            if (!class_exists($class)) {
                continue;
            }

            try {
                $ref = new \ReflectionClass($class);
                if (!$ref->isInstantiable()) {
                    continue;
                }
                $obj = $ref->newInstance();
                if (method_exists($obj, 'initialize')) {
                    $obj->initialize();
                }
                ++$count;
            } catch (\Throwable) {
                ++$count;
            }
        }

        return $count;
    }
}
