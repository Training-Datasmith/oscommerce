<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Customers\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
/**
 * @since v3.0.2
 */
class Get
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $sql_query = 'select * from :table_customers where ';
        if (isset($data['email_address'])) {
            $sql_query .= 'customers_email_address = :customers_email_address';
        } else {
            $sql_query .= 'customers_id = :customers_id';
        }
        $Qcustomer = $OSCOM_PDO->prepare($sql_query);
        if (isset($data['email_address'])) {
            $Qcustomer->bind_value(':customers_email_address', $data['email_address']);
        } else {
            $Qcustomer->bind_int(':customers_id', $data['id']);
        }
        $Qcustomer->execute();
        if ($Qcustomer->fetch() !== false) {
            $result = $Qcustomer->to_array();
            $result['customers_name'] = $result['customers_firstname'] . ' ' . $result['customers_lastname'];
        }
        return $result;
    }
}