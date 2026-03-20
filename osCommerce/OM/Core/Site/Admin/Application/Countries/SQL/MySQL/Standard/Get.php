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
class Get
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qcountries = $OSCOM_PDO->prepare('select c.*, count(z.zone_id) as total_zones2 from :table_countries c left join :table_zones z on (c.countries_id = z.zone_country_id) where c.countries_id = :countries_id');
        $Qcountries->bind_int(':countries_id', $data['id']);
        $Qcountries->execute();
        return $Qcountries->fetch();
    }
}