<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Languages\Model;

use Os_Commerce\OM\Core\Cache;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Languages\Languages;
class delete
{
    public static function execute($id)
    {
        $data = ['id' => $id];
        if (Languages::get($id, 'code') != DEFAULT_LANGUAGE && OSCOM::call_db('Admin\Languages\Delete', $data)) {
            Cache::clear('languages');
            return true;
        }
        return false;
    }
}