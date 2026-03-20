<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Services\Model;

use Os_Commerce\OM\Core\Cache;
use Os_Commerce\OM\Core\OSCOM;
/**
 * @since v3.0.2
 */
class save
{
    public static function execute($data)
    {
        if (OSCOM::call_db('Admin\Services\Save', $data)) {
            Cache::clear('configuration');
            return true;
        }
        return false;
    }
}