<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Languages\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Get_Definition
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qdef = $OSCOM_PDO->prepare('select * from :table_languages_definitions where id = :id');
        $Qdef->bind_int(':id', $data['id']);
        $Qdef->execute();
        return $Qdef->fetch();
    }
}