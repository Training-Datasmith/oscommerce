<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Shop;

use Os_Commerce\OM\Core\Hash;
use Os_Commerce\OM\Core\Mail;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
/**
 * The Account class manages customer accounts
 */
class Account
{
    /**
     * Returns the account information for the current customer
     *
     * @access public
     * @return object
     */
    public static function get_entry()
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Customer = Registry::get('Customer');
        $Qaccount = $OSCOM_PDO->prepare('select customers_gender, customers_firstname, customers_lastname, date_format(customers_dob, "%Y") as customers_dob_year, date_format(customers_dob, "%m") as customers_dob_month, date_format(customers_dob, "%d") as customers_dob_date, customers_email_address from :table_customers where customers_id = :customers_id');
        $Qaccount->bind_int(':customers_id', $OSCOM_Customer->get_id());
        $Qaccount->execute();
        return $Qaccount;
    }
    /**
     * Returns the customer ID from a given email address
     *
     * @param string $email_address The customers email address
     * @access public
     */
    public static function get_id($email_address)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Quser = $OSCOM_PDO->prepare('select customers_id from :table_customers where customers_email_address = :customers_email_address limit 1');
        $Quser->bind_value(':customers_email_address', $email_address);
        $Quser->execute();
        $result = $Quser->fetch();
        if ($result !== false) {
            return $result['customers_id'];
        }
        return false;
    }
    /**
     * Stores a new customer account entry in the database
     *
     * @param array $data An array containing the customers information
     * @access public
     * @return boolean
     */
    public static function create_entry($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Session = Registry::get('Session');
        $OSCOM_Customer = Registry::get('Customer');
        $oscom_shopping_cart = Registry::get('ShoppingCart');
        $oscom_navigation_history = Registry::get('NavigationHistory');
        $Qcustomer = $OSCOM_PDO->prepare('insert into :table_customers (customers_firstname, customers_lastname, customers_email_address, customers_newsletter, customers_status, customers_ip_address, customers_password, customers_gender, customers_dob, number_of_logons, date_account_created) values (:customers_firstname, :customers_lastname, :customers_email_address, :customers_newsletter, :customers_status, :customers_ip_address, :customers_password, :customers_gender, :customers_dob, :number_of_logons, now())');
        $Qcustomer->bind_value(':customers_firstname', $data['firstname']);
        $Qcustomer->bind_value(':customers_lastname', $data['lastname']);
        $Qcustomer->bind_value(':customers_email_address', $data['email_address']);
        $Qcustomer->bind_value(':customers_newsletter', isset($data['newsletter']) && $data['newsletter'] == '1' ? '1' : '');
        $Qcustomer->bind_value(':customers_status', '1');
        $Qcustomer->bind_value(':customers_ip_address', OSCOM::get_ip_address());
        $Qcustomer->bind_value(':customers_password', Hash::get($data['password']));
        $Qcustomer->bind_value(':customers_gender', ACCOUNT_GENDER > -1 && isset($data['gender']) && ($data['gender'] == 'm' || $data['gender'] == 'f') ? $data['gender'] : '');
        $Qcustomer->bind_value(':customers_dob', ACCOUNT_DATE_OF_BIRTH == '1' ? date('Ymd', $data['dob']) : '');
        $Qcustomer->bind_int(':number_of_logons', 0);
        $Qcustomer->execute();
        if ($Qcustomer->row_count() === 1) {
            $customer_id = $OSCOM_PDO->last_insert_id();
            if (SERVICE_SESSION_REGENERATE_ID == '1') {
                $OSCOM_Session->recreate();
            }
            $OSCOM_Customer->set_customer_data($customer_id);
            // restore cart contents
            $oscom_shopping_cart->synchronize_with_database();
            $oscom_navigation_history->remove_current_page();
            // build the welcome email content
            if (ACCOUNT_GENDER > -1 && isset($data['gender'])) {
                if ($data['gender'] == 'm') {
                    $email_text = sprintf(OSCOM::get_def('email_addressing_gender_male'), $OSCOM_Customer->get_last_name()) . "\n\n";
                } else {
                    $email_text = sprintf(OSCOM::get_def('email_addressing_gender_female'), $OSCOM_Customer->get_last_name()) . "\n\n";
                }
            } else {
                $email_text = sprintf(OSCOM::get_def('email_addressing_gender_unknown'), $OSCOM_Customer->get_name()) . "\n\n";
            }
            $email_text .= sprintf(OSCOM::get_def('email_create_account_body'), STORE_NAME, STORE_OWNER_EMAIL_ADDRESS);
            $c_email = new Mail($OSCOM_Customer->get_name(), $OSCOM_Customer->get_email_address(), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, sprintf(OSCOM::get_def('email_create_account_subject'), STORE_NAME));
            $c_email->set_body_plain($email_text);
            $c_email->send();
            return true;
        }
        return false;
    }
    /**
     * Update the current customer account record in the database
     *
     * @param array $data An array containing the customer account information
     * @access public
     * @return boolean
     */
    public static function save_entry($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Customer = Registry::get('Customer');
        $Qcustomer = $OSCOM_PDO->prepare('update :table_customers set customers_gender = :customers_gender, customers_firstname = :customers_firstname, customers_lastname = :customers_lastname, customers_email_address = :customers_email_address, customers_dob = :customers_dob, date_account_last_modified = now() where customers_id = :customers_id');
        $Qcustomer->bind_value(':customers_gender', ACCOUNT_GENDER > -1 && isset($data['gender']) && ($data['gender'] == 'm' || $data['gender'] == 'f') ? $data['gender'] : '');
        $Qcustomer->bind_value(':customers_firstname', $data['firstname']);
        $Qcustomer->bind_value(':customers_lastname', $data['lastname']);
        $Qcustomer->bind_value(':customers_email_address', $data['email_address']);
        $Qcustomer->bind_value(':customers_dob', ACCOUNT_DATE_OF_BIRTH == '1' ? date('Ymd', $data['dob']) : '');
        $Qcustomer->bind_int(':customers_id', $OSCOM_Customer->get_id());
        $Qcustomer->execute();
        return $Qcustomer->row_count() === 1;
    }
    /**
     * Updates the password in a customers account
     *
     * @param string $password The new password
     * @param integer $customer_id The ID of the customer account to update
     * @access public
     * @return boolean
     */
    public static function save_password($password, $customer_id = null)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Customer = Registry::get('Customer');
        if (!is_numeric($customer_id)) {
            $customer_id = $OSCOM_Customer->get_id();
        }
        $Qcustomer = $OSCOM_PDO->prepare('update :table_customers set customers_password = :customers_password, date_account_last_modified = now() where customers_id = :customers_id');
        $Qcustomer->bind_value(':customers_password', Hash::get($password));
        $Qcustomer->bind_int(':customers_id', $customer_id);
        $Qcustomer->execute();
        return $Qcustomer->row_count() === 1;
    }
    /**
     * Checks if a customer account record exists with the provided e-mail address
     *
     * @param string $email_address The e-mail address to check for
     * @access public
     * @return boolean
     */
    public static function check_entry($email_address)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qcheck = $OSCOM_PDO->prepare('select customers_id from :table_customers where customers_email_address = :customers_email_address limit 1');
        $Qcheck->bind_value(':customers_email_address', $email_address);
        $Qcheck->execute();
        return $Qcheck->fetch() !== false;
    }
    /**
     * Checks if a password matches the current or provided customer account
     *
     * @param string $password The unencrypted password to confirm
     * @param string $email_address The email address of the customer account to check against
     * @access public
     * @return boolean
     */
    public static function check_password($password, $email_address = null)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Customer = Registry::get('Customer');
        if (empty($email_address)) {
            $Qcheck = $OSCOM_PDO->prepare('select customers_password from :table_customers where customers_id = :customers_id');
            $Qcheck->bind_int(':customers_id', $OSCOM_Customer->get_id());
            $Qcheck->execute();
        } else {
            $Qcheck = $OSCOM_PDO->prepare('select customers_password from :table_customers where customers_email_address = :customers_email_address limit 1');
            $Qcheck->bind_value(':customers_email_address', $email_address);
            $Qcheck->execute();
        }
        $result = $Qcheck->fetch();
        if ($result !== false) {
            return Hash::validate($password, $Qcheck->value('customers_password'));
        }
        return false;
    }
    /**
     * Checks if an e-mail address already exists in another customer account record
     *
     * @param string $email_address The e-mail address to check
     * @access public
     * @return boolean
     */
    public static function check_duplicate_entry($email_address)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Customer = Registry::get('Customer');
        $Qcheck = $OSCOM_PDO->prepare('select customers_id from :table_customers where customers_email_address = :customers_email_address and customers_id != :customers_id limit 1');
        $Qcheck->bind_value(':customers_email_address', $email_address);
        $Qcheck->bind_int(':customers_id', $OSCOM_Customer->get_id());
        $Qcheck->execute();
        return $Qcheck->fetch() !== false;
    }
    /**
     * Perform a login
     *
     * @param string $email_address The e-mail address to login with
     * @param string $password The password to verify the account with
     * @access public
     * @return boolean
     */
    public static function log_in($email_address, $password)
    {
        $OSCOM_Session = Registry::get('Session');
        $OSCOM_Customer = Registry::get('Customer');
        $OSCOM_PDO = Registry::get('PDO');
        $oscom_shopping_cart = Registry::get('ShoppingCart');
        if (self::check_entry($email_address) && self::check_password($password, $email_address)) {
            if (SERVICE_SESSION_REGENERATE_ID == '1') {
                $OSCOM_Session->recreate();
            }
            $OSCOM_Customer->set_customer_data(self::get_id($email_address));
            $Qupdate = $OSCOM_PDO->prepare('update :table_customers set date_last_logon = now(), number_of_logons = number_of_logons+1 where customers_id = :customers_id');
            $Qupdate->bind_int(':customers_id', $OSCOM_Customer->get_id());
            $Qupdate->execute();
            $oscom_shopping_cart->synchronize_with_database();
            return true;
        }
        return false;
    }
}