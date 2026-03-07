<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core;

abstract class ApplicationModelAbstract
{
    public static function __callStatic($name, $arguments)
    {
        $class = get_called_class();

        $ns = substr($class, 0, strrpos($class, '\\'));

        return call_user_func_array([$ns . '\\Model\\' . $name, 'execute'], $arguments);
    }
}
