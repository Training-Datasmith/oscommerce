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
class Delete_Module
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qdel = $OSCOM_PDO->prepare('delete from :table_modules where code = :code and modules_group = :modules_group');
        $Qdel->bind_value(':code', $data['code']);
        $Qdel->bind_value(':modules_group', $data['group']);
        $Qdel->execute();
        return !$Qdel->is_error();
    }
}