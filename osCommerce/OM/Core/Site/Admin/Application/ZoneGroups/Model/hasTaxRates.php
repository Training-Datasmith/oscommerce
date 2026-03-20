<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Zone_Groups\Model;

use Os_Commerce\OM\Core\OSCOM;
class Has_Tax_Rates
{
    public static function execute($tax_zone_id)
    {
        $data = ['tax_zone_id' => $tax_zone_id];
        $result = OSCOM::call_db('Admin\ZoneGroups\GetTotalTaxRates', $data);
        return $result > 0;
    }
}