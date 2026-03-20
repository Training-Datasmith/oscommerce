<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Insert_Module
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qinstall = $OSCOM_PDO->prepare('insert into :table_modules (title, code, author_name, author_www, modules_group) values (:title, :code, :author_name, :author_www, :modules_group)');
        $Qinstall->bind_value(':title', $data['title']);
        $Qinstall->bind_value(':code', $data['code']);
        $Qinstall->bind_value(':author_name', $data['author_name']);
        $Qinstall->bind_value(':author_www', $data['author_www']);
        $Qinstall->bind_value(':modules_group', $data['group']);
        $Qinstall->execute();
        return !$Qinstall->is_error();
    }
}