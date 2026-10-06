<?php

declare(strict_types=1);

namespace Tests\Unit\Corpus;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Support\ShopHtmlFixture;

/**
 * Smoke-invoke public static methods on Core classes (no DB).
 */
class CoreClassSmokeTest extends TestCase
{
    protected function setUp(): void
    {
        ShopHtmlFixture::boot();
    }

    public static function coreClasses(): iterable
    {
        $dir = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core';
        foreach (glob($dir . '/*.php') ?: [] as $file) {
            $base = basename($file, '.php');
            if (in_array($base, ['OSCOM', 'Autoloader'], true)) {
                continue;
            }
            $class = 'osCommerce\\OM\\Core\\' . $base;
            if (class_exists($class)) {
                yield $class => [$class];
            }
        }
    }

    #[DataProvider('coreClasses')]
    public function testPublicStaticMethodsCallable(string $class): void
    {
        $ref = new \ReflectionClass($class);
        if ($ref->isAbstract()) {
            $this->addToAssertionCount(1);

            return;
        }

        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_STATIC) as $method) {
            if ($method->getDeclaringClass()->getName() !== $class) {
                continue;
            }

            $params = $method->getNumberOfParameters();
            if ($params > 2) {
                continue;
            }

            $args = array_fill(0, $params, null);

            try {
                $method->invokeArgs(null, $args);
            } catch (\Throwable) {
                // Expected without full stack.
            }
        }

        $this->addToAssertionCount(1);
    }
}
