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
class Zone_Get
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qzones = $OSCOM_PDO->prepare('select * from :table_zones where zone_id = :zone_id');
        $Qzones->bind_int(':zone_id', $data['id']);
        $Qzones->execute();
        return $Qzones->fetch();
    }
}