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
class Get_Total_Tax_Rates
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qtotal = $OSCOM_PDO->prepare('select count(*) as total from :table_tax_rates where tax_zone_id = :tax_zone_id');
        $Qtotal->bind_int(':tax_zone_id', $data['tax_zone_id']);
        $Qtotal->execute();
        $result = $Qtotal->fetch();
        return $result['total'];
    }
}