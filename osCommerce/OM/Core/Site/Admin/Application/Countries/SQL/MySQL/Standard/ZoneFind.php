<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Countries\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Zone_Find
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $Qzones = $OSCOM_PDO->prepare('select * from :table_zones where zone_country_id = :zone_country_id and (zone_name like :zone_name or zone_code like :zone_code) order by zone_name');
        $Qzones->bind_int(':zone_country_id', $data['country_id']);
        $Qzones->bind_value(':zone_name', '%' . $data['keywords'] . '%');
        $Qzones->bind_value(':zone_code', '%' . $data['keywords'] . '%');
        $Qzones->execute();
        $result['entries'] = $Qzones->fetch_all();
        $result['total'] = count($result['entries']);
        return $result;
    }
}