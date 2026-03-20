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
class Entry_Find
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $Qentries = $OSCOM_PDO->prepare('select z2gz.*, c.countries_name, z.zone_name from :table_zones_to_geo_zones z2gz, :table_countries c, :table_zones z where z2gz.geo_zone_id = :geo_zone_id and z2gz.zone_country_id = c.countries_id and z2gz.zone_id = z.zone_id and (c.countries_name like :countries_name or z.zone_name like :zone_name) order by c.countries_name, z.zone_name');
        $Qentries->bind_int(':geo_zone_id', $data['group_id']);
        $Qentries->bind_value(':countries_name', '%' . $data['keywords'] . '%');
        $Qentries->bind_value(':zone_name', '%' . $data['keywords'] . '%');
        $Qentries->execute();
        $result['entries'] = $Qentries->fetch_all();
        $result['total'] = count($result['entries']);
        return $result;
    }
}