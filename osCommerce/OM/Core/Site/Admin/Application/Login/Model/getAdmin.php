<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Login\Model;

use Os_Commerce\OM\Core\OSCOM;
class Get_Admin
{
    public static function execute($username, $key = null)
    {
        $data = ['username' => $username];
        $result = OSCOM::call_db('Admin\Login\GetAdmin', $data);
        if (isset($key)) {
            $result = $result[$key] ?: null;
        }
        return $result;
    }
}