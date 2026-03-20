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
class Save
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        if (!isset($data['id'])) {
            $data['id'] = null;
        }
        $error = false;
        $OSCOM_PDO->begin_transaction();
        if (is_numeric($data['id'])) {
            $Qcustomer = $OSCOM_PDO->prepare('update :table_customers set customers_gender = :customers_gender, customers_firstname = :customers_firstname, customers_lastname = :customers_lastname, customers_email_address = :customers_email_address, customers_dob = :customers_dob, customers_newsletter = :customers_newsletter, customers_status = :customers_status, date_account_last_modified = now() where customers_id = :customers_id');
            $Qcustomer->bind_int(':customers_id', $data['id']);
        } else {
            $Qcustomer = $OSCOM_PDO->prepare('insert into :table_customers (customers_gender, customers_firstname, customers_lastname, customers_email_address, customers_dob, customers_newsletter, customers_status, number_of_logons, date_account_created) values (:customers_gender, :customers_firstname, :customers_lastname, :customers_email_address, :customers_dob, :customers_newsletter, :customers_status, :number_of_logons, now())');
            $Qcustomer->bind_int(':number_of_logons', 0);
        }
        $Qcustomer->bind_value(':customers_gender', $data['gender']);
        $Qcustomer->bind_value(':customers_firstname', $data['firstname']);
        $Qcustomer->bind_value(':customers_lastname', $data['lastname']);
        $Qcustomer->bind_value(':customers_email_address', $data['email_address']);
        $Qcustomer->bind_value(':customers_dob', $data['dob_year'] . '-' . $data['dob_month'] . '-' . $data['dob_day'] . ' 00:00:00');
        $Qcustomer->bind_int(':customers_newsletter', $data['newsletter']);
        $Qcustomer->bind_int(':customers_status', $data['status']);
        $Qcustomer->execute();
        if (!$Qcustomer->is_error()) {
            if (!empty($data['password'])) {
                $customer_id = is_numeric($data['id']) ? $data['id'] : $OSCOM_PDO->last_insert_id();
                $Qpassword = $OSCOM_PDO->prepare('update :table_customers set customers_password = :customers_password where customers_id = :customers_id');
                $Qpassword->bind_value(':customers_password', $data['password']);
                $Qpassword->bind_int(':customers_id', $customer_id);
                $Qpassword->execute();
                if ($Qpassword->is_error()) {
                    $error = true;
                }
            }
        }
        if ($error === false) {
            $OSCOM_PDO->commit();
            return true;
        }
        $OSCOM_PDO->roll_back();
        return false;
    }
}