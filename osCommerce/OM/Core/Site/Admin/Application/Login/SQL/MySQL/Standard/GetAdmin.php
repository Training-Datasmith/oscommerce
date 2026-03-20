<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Login\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Get_Admin
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qadmin = $OSCOM_PDO->prepare('select id, user_name, user_password from :table_administrators where user_name = :user_name limit 1');
        $Qadmin->bind_value(':user_name', $data['username']);
        $Qadmin->execute();
        return $Qadmin->fetch();
    }
}