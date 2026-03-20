<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Administrators\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Get
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qadmin = $OSCOM_PDO->prepare('select * from :table_administrators where id = :id');
        $Qadmin->bind_int(':id', $data['id']);
        $Qadmin->execute();
        $result = $Qadmin->fetch();
        $result['access_modules'] = [];
        $Qaccess = $OSCOM_PDO->prepare('select module from :table_administrators_access where administrators_id = :administrators_id');
        $Qaccess->bind_int(':administrators_id', $data['id']);
        $Qaccess->execute();
        while ($row = $Qaccess->fetch()) {
            $result['access_modules'][] = $row['module'];
        }
        return $result;
    }
}