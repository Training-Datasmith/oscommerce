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
class Email_Address_Exists
{
    public static function execute($email_address, $customer_id = null)
    {
        $data = ['email_address' => $email_address];
        $result = OSCOM::call_db('Admin\Customers\Get', $data);
        if (isset($customer_id)) {
            return $result['customers_id'] != $customer_id;
        }
        return !empty($result);
    }
}