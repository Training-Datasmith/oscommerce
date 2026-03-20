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
class Entry_Save
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        if (isset($data['id']) && is_numeric($data['id'])) {
            $Qentry = $OSCOM_PDO->prepare('update :table_zones_to_geo_zones set zone_country_id = :zone_country_id, zone_id = :zone_id, last_modified = now() where association_id = :association_id');
            $Qentry->bind_int(':association_id', $data['id']);
        } else {
            $Qentry = $OSCOM_PDO->prepare('insert into :table_zones_to_geo_zones (zone_country_id, zone_id, geo_zone_id, date_added) values (:zone_country_id, :zone_id, :geo_zone_id, now())');
            $Qentry->bind_int(':geo_zone_id', $data['group_id']);
        }
        if (is_numeric($data['country_id'])) {
            $Qentry->bind_int(':zone_country_id', $data['country_id']);
        } else {
            $Qentry->bind_null(':zone_country_id');
        }
        if (is_numeric($data['zone_id'])) {
            $Qentry->bind_int(':zone_id', $data['zone_id']);
        } else {
            $Qentry->bind_null(':zone_id');
        }
        $Qentry->execute();
        return $Qentry->row_count() === 1 || !$Qentry->is_error();
    }
}