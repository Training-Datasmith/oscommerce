<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Zone_Groups\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Entry_Delete
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qentry = $OSCOM_PDO->prepare('delete from :table_zones_to_geo_zones where association_id = :association_id');
        $Qentry->bind_int(':association_id', $data['id']);
        $Qentry->execute();
        return $Qentry->row_count() === 1;
    }
}