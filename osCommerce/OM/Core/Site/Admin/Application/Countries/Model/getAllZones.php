<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Countries\Model;

use Os_Commerce\OM\Core\OSCOM;
class Get_All_Zones
{
    public static function execute($country_id)
    {
        $data = ['country_id' => $country_id];
        return OSCOM::call_db('Admin\Countries\ZoneGetAll', $data);
    }
}