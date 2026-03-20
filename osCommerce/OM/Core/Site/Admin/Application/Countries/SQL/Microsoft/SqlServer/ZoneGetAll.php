<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Countries\SQL\Microsoft\Sql_Server;

use Os_Commerce\OM\Core\Registry;
class Zone_Get_All
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $Qzones = $OSCOM_PDO->prepare('select * from :table_zones where zone_country_id = :zone_country_id order by zone_name');
        $Qzones->bind_int(':zone_country_id', $data['country_id']);
        $Qzones->execute();
        $result['entries'] = $Qzones->get_all();
        $result['total'] = count($result['entries']);
        return $result;
    }
}