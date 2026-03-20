<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Administrators\Model;

use Os_Commerce\OM\Core\Hash;
use Os_Commerce\OM\Core\OSCOM;
class save
{
    public static function execute($data)
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::get(trim($data['password']));
        }
        return OSCOM::call_db('Admin\Administrators\Save', $data);
    }
}