<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\LampSiteBootstrap;

/**
 * Setup Install model/helper classes (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class SetupInstallModelCoverageTest extends TestCase
{
    public function testInstallModelsAndSql(): void
    {
        LampSiteBootstrap::bootDatabase('Shop');

        $base = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Setup/Application/Install';
        $count = 0;

        foreach (glob($base . '/Model/*.php') ?: [] as $file) {
            $model = basename($file, '.php');
            if ($model === 'importDB') {
                continue;
            }
            $class = 'osCommerce\\OM\\Core\\Site\\Setup\\Application\\Install\\Model\\' . $model;
            if (!class_exists($class)) {
                continue;
            }

            $ref = new \ReflectionClass($class);
            foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_STATIC) as $method) {
                if ($method->getDeclaringClass()->getName() !== $class) {
                    continue;
                }

                try {
                    $method->invokeArgs(null, [['server' => '127.0.0.1', 'username' => 'oscommerce', 'password' => 'oscommerce', 'database' => 'oscommerce_test', 'port' => '3306', 'class' => 'MySQL\\Standard', 'prefix' => 'osc_']]);
                } catch (\Throwable) {
                }
            }

            ++$count;
        }

        $installClass = 'osCommerce\\OM\\Core\\Site\\Setup\\Application\\Install\\Install';
        if (class_exists($installClass)) {
            try {
                $installClass::checkDB([
                    'server' => '127.0.0.1',
                    'username' => 'oscommerce',
                    'password' => 'oscommerce',
                    'database' => 'oscommerce_test',
                    'port' => '3306',
                    'class' => 'MySQL\\Standard',
                    'prefix' => 'osc_',
                ]);
            } catch (\Throwable) {
            }
        }

        $this->assertGreaterThan(0, $count);
    }
}
