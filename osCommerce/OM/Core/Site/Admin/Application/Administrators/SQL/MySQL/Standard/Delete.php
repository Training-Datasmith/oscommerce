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
class Delete
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qdel = $OSCOM_PDO->prepare('delete from :table_administrators where id = :id');
        $Qdel->bind_int(':id', $data['id']);
        $Qdel->execute();
        return $Qdel->row_count() === 1;
    }
}