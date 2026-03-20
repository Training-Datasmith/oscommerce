<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Tax_Classes\Model;

use Os_Commerce\OM\Core\OSCOM;
class Get_Number_Of_Tax_Rates
{
    public static function execute($id)
    {
        $data = ['id' => $id];
        $result = OSCOM::call_db('Admin\TaxClasses\Get', $data);
        return $result['total_tax_rates'];
    }
}