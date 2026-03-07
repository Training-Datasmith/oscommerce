<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core;

class Registry
{
    private static $_data = [];

    public static function get($key)
    {
        if (substr($key, 0, 6) != 'OSCOM_') {
            $key = 'OSCOM_' . $key;
        }

        if (!self::exists($key)) {
            trigger_error('OSCOM_Registry::get - ' . $key . ' is not registered');

            return false;
        }

        return self::$_data[$key];
    }

    public static function set($key, $value, $force = false)
    {
        if (substr($key, 0, 6) != 'OSCOM_') {
            $key = 'OSCOM_' . $key;
        }

        if (self::exists($key) && ($force !== true)) {
            trigger_error('OSCOM_Registry::set - ' . $key . ' already registered and is not forced to be replaced');

            return false;
        }

        $GLOBALS[$key] = self::$_data[$key] = $value; // GLOBALS used in template files
    }

    public static function exists($key)
    {
        if (substr($key, 0, 6) != 'OSCOM_') {
            $key = 'OSCOM_' . $key;
        }

        return array_key_exists($key, self::$_data);
    }
}
