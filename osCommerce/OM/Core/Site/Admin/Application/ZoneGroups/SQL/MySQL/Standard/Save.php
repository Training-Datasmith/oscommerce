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
class Save
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        if (isset($data['id']) && is_numeric($data['id'])) {
            $Qzone = $OSCOM_PDO->prepare('update :table_geo_zones set geo_zone_name = :geo_zone_name, geo_zone_description = :geo_zone_description, last_modified = now() where geo_zone_id = :geo_zone_id');
            $Qzone->bind_int(':geo_zone_id', $data['id']);
        } else {
            $Qzone = $OSCOM_PDO->prepare('insert into :table_geo_zones (geo_zone_name, geo_zone_description, date_added) values (:geo_zone_name, :geo_zone_description, now())');
        }
        $Qzone->bind_value(':geo_zone_name', $data['zone_name']);
        $Qzone->bind_value(':geo_zone_description', $data['zone_description']);
        $Qzone->execute();
        return $Qzone->row_count() === 1 || !$Qzone->is_error();
    }
}