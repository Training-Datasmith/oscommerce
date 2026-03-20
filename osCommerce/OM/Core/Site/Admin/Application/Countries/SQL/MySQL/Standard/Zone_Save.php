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
class Zone_Save
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        if (isset($data['id']) && is_numeric($data['id'])) {
            $Qzone = $OSCOM_PDO->prepare('update :table_zones set zone_name = :zone_name, zone_code = :zone_code, zone_country_id = :zone_country_id where zone_id = :zone_id');
            $Qzone->bind_int(':zone_id', $data['id']);
        } else {
            $Qzone = $OSCOM_PDO->prepare('insert into :table_zones (zone_name, zone_code, zone_country_id) values (:zone_name, :zone_code, :zone_country_id)');
        }
        $Qzone->bind_value(':zone_name', $data['name']);
        $Qzone->bind_value(':zone_code', $data['code']);
        $Qzone->bind_int(':zone_country_id', $data['country_id']);
        $Qzone->execute();
        return $Qzone->row_count() === 1 || !$Qzone->is_error();
    }
}