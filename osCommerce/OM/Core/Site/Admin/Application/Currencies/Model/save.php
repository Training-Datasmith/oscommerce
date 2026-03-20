<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Currencies\Model;

use Os_Commerce\OM\Core\Cache;
use Os_Commerce\OM\Core\OSCOM;
class save
{
    public static function execute($data)
    {
        if (OSCOM::call_db('Admin\Currencies\Save', $data)) {
            Cache::clear('currencies');
            if ($data['set_default'] === true) {
                Cache::clear('configuration');
            }
            return true;
        }
        return false;
    }
}