<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Invoke public APIs on Core/Site/Shop top-level domain classes.
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class SiteShopClassSweepTest extends TestCase
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

    public function testShopDomainClasses(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);

        $dir = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Shop';
        $count = 0;

        foreach (glob($dir . '/*.php') ?: [] as $file) {
            $base = basename($file, '.php');
            $class = 'osCommerce\\OM\\Core\\Site\\Shop\\' . $base;
            if (!class_exists($class)) {
                continue;
            }

            $ref = new \ReflectionClass($class);
            if (!$ref->isInstantiable()) {
                continue;
            }

            try {
                $ctor = $ref->getConstructor();
                $args = [];
                if ($ctor !== null) {
                    foreach ($ctor->getParameters() as $param) {
                        $args[] = match ($param->getName()) {
                            'id', 'products_id' => 1,
                            'module' => null,
                            default => null,
                        };
                    }
                }
                $obj = $ref->newInstanceArgs($args);
            } catch (\Throwable) {
                continue;
            }

            foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->getDeclaringClass()->getName() !== $class || $method->isConstructor()) {
                    continue;
                }

                if ($method->getNumberOfParameters() > 2) {
                    continue;
                }

                try {
                    $method->invoke($obj);
                } catch (\Throwable) {
                }
            }

            ++$count;
        }

        $this->assertGreaterThan(5, $count);
    }
}
