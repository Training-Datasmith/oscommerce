<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Customers\Model;

use Os_Commerce\OM\Core\OSCOM;
/**
 * @since v3.0.2
 */
class Get_Address_Book
{
    public static function execute($customer_id)
    {
        $data = ['customer_id' => $customer_id];
        return OSCOM::call_db('Admin\Customers\GetAddressBook', $data);
    }
}