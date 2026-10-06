<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\Registry;

final class RegistryTestHelper
{
    public static function reset(): void
    {
        $ref = new \ReflectionClass(Registry::class);
        $prop = $ref->getProperty('_data');
        $prop->setAccessible(true);
        $prop->setValue(null, []);

        foreach (array_keys($GLOBALS) as $key) {
            if (str_starts_with($key, 'OSCOM_')) {
                unset($GLOBALS[$key]);
            }
        }
    }
}
