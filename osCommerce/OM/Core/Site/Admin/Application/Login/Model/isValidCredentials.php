<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Login\Model;

use Os_Commerce\OM\Core\Hash;
use Os_Commerce\OM\Core\OSCOM;
class Is_Valid_Credentials
{
    public static function execute($data)
    {
        $result = OSCOM::call_db('Admin\Login\GetAdmin', ['username' => $data['username']]);
        if (!empty($result)) {
            return Hash::validate($data['password'], $result['user_password']);
        }
        return false;
    }
}