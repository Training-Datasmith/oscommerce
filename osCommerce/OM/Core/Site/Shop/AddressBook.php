<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Shop;

use Os_Commerce\OM\Core\Registry;
/**
 * The osC_AddressBook class handles customer address book related functions
 */
class Address_Book
{
    /**
     * Returns the address book entries for the current customer
     *
     * @access public
     * @return array
     */
    public static function get_listing()
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Customer = Registry::get('Customer');
        $Qaddresses = $OSCOM_PDO->prepare('select ab.address_book_id, ab.entry_firstname as firstname, ab.entry_lastname as lastname, ab.entry_company as company, ab.entry_street_address as street_address, ab.entry_suburb as suburb, ab.entry_city as city, ab.entry_postcode as postcode, ab.entry_state as state, ab.entry_zone_id as zone_id, ab.entry_country_id as country_id, z.zone_code as zone_code, c.countries_name as country_title from :table_address_book ab left join :table_zones z on (ab.entry_zone_id = z.zone_id), :table_countries c where ab.customers_id = :customers_id and ab.entry_country_id = c.countries_id order by ab.entry_firstname, ab.entry_lastname');
        $Qaddresses->bind_int(':customers_id', $OSCOM_Customer->get_id());
        $Qaddresses->execute();
        return $Qaddresses;
    }
    /**
     * Returns a specific address book entry for the current customer
     *
     * @param int $id The ID of the address book entry to return
     * @access public
     * @return array
     */
    public static function get_entry($id)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Customer = Registry::get('Customer');
        $Qentry = $OSCOM_PDO->prepare('select entry_gender as gender, entry_company as company, entry_firstname as firstname, entry_lastname as lastname, entry_street_address as street_address, entry_suburb as suburb, entry_postcode as postcode, entry_city as city, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id, entry_telephone as telephone, entry_fax as fax from :table_address_book where address_book_id = :address_book_id and customers_id = :customers_id');
        $Qentry->bind_int(':address_book_id', $id);
        $Qentry->bind_int(':customers_id', $OSCOM_Customer->get_id());
        $Qentry->execute();
        return $Qentry->to_array();
    }
    /**
     * Verify the address book entry belongs to the current customer
     *
     * @param int $id The ID of the address book entry to verify
     * @access public
     * @return boolean
     */
    public static function check_entry($id)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Customer = Registry::get('Customer');
        $Qentry = $OSCOM_PDO->prepare('select address_book_id from :table_address_book where address_book_id = :address_book_id and customers_id = :customers_id');
        $Qentry->bind_int(':address_book_id', $id);
        $Qentry->bind_int(':customers_id', $OSCOM_Customer->get_id());
        $Qentry->execute();
        return $Qentry->fetch() !== false;
    }
    /**
     * Return the number of address book entries the current customer has
     *
     * @access public
     * @return integer
     */
    public static function number_of_entries()
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Customer = Registry::get('Customer');
        static $total_entries;
        if (!isset($total_entries)) {
            $total_entries = 0;
            if ($OSCOM_Customer->is_logged_on()) {
                $Qaddresses = $OSCOM_PDO->prepare('select count(*) as total from :table_address_book where customers_id = :customers_id');
                $Qaddresses->bind_int(':customers_id', $OSCOM_Customer->get_id());
                $Qaddresses->execute();
                $total_entries = $Qaddresses->value_int('total');
            }
        }
        return $total_entries;
    }
    /**
     * Save an address book entry
     *
     * @param array $data An array containing the address book information
     * @param int $id The ID of the address book entry to update (if this is not provided, a new address book entry is created)
     * @access public
     * @return boolean
     */
    public static function save_entry($data, $id = '')
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Customer = Registry::get('Customer');
        $updated_record = false;
        if (is_numeric($id)) {
            $Qab = $OSCOM_PDO->prepare('update :table_address_book set customers_id = :customers_id, entry_gender = :entry_gender, entry_company = :entry_company, entry_firstname = :entry_firstname, entry_lastname = :entry_lastname, entry_street_address = :entry_street_address, entry_suburb = :entry_suburb, entry_postcode = :entry_postcode, entry_city = :entry_city, entry_state = :entry_state, entry_country_id = :entry_country_id, entry_zone_id = :entry_zone_id, entry_telephone = :entry_telephone, entry_fax = :entry_fax where address_book_id = :address_book_id and customers_id = :customers_id');
            $Qab->bind_int(':address_book_id', $id);
            $Qab->bind_int(':customers_id', $OSCOM_Customer->get_id());
        } else {
            $Qab = $OSCOM_PDO->prepare('insert into :table_address_book (customers_id, entry_gender, entry_company, entry_firstname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_country_id, entry_zone_id, entry_telephone, entry_fax) values (:customers_id, :entry_gender, :entry_company, :entry_firstname, :entry_lastname, :entry_street_address, :entry_suburb, :entry_postcode, :entry_city, :entry_state, :entry_country_id, :entry_zone_id, :entry_telephone, :entry_fax)');
        }
        $Qab->bind_int(':customers_id', $OSCOM_Customer->get_id());
        $Qab->bind_value(':entry_gender', ACCOUNT_GENDER > -1 && isset($data['gender']) && ($data['gender'] == 'm' || $data['gender'] == 'f') ? $data['gender'] : '');
        $Qab->bind_value(':entry_company', ACCOUNT_COMPANY > -1 ? $data['company'] : '');
        $Qab->bind_value(':entry_firstname', $data['firstname']);
        $Qab->bind_value(':entry_lastname', $data['lastname']);
        $Qab->bind_value(':entry_street_address', $data['street_address']);
        $Qab->bind_value(':entry_suburb', ACCOUNT_SUBURB > -1 ? $data['suburb'] : '');
        $Qab->bind_value(':entry_postcode', ACCOUNT_POST_CODE > -1 ? $data['postcode'] : '');
        $Qab->bind_value(':entry_city', $data['city']);
        $Qab->bind_value(':entry_state', ACCOUNT_STATE > -1 ? isset($data['zone_id']) && $data['zone_id'] > 0 ? '' : $data['state'] : '');
        $Qab->bind_int(':entry_country_id', $data['country']);
        if (isset($data['zone_id']) && is_numeric($data['zone_id'])) {
            $Qab->bind_int(':entry_zone_id', $data['zone_id']);
        } else {
            $Qab->bind_null(':entry_zone_id');
        }
        $Qab->bind_value(':entry_telephone', ACCOUNT_TELEPHONE > -1 ? $data['telephone'] : '');
        $Qab->bind_value(':entry_fax', ACCOUNT_FAX > -1 ? $data['fax'] : '');
        $Qab->execute();
        if ($Qab->row_count() === 1) {
            $updated_record = true;
        }
        if (isset($data['primary']) && $data['primary'] === true) {
            if (!is_numeric($id)) {
                $id = $OSCOM_PDO->last_insert_id();
            }
            if (self::set_primary_address($id)) {
                $OSCOM_Customer->set_country_id($data['country']);
                $OSCOM_Customer->set_zone_id($data['zone_id'] > 0 ? (int) $data['zone_id'] : '0');
                $OSCOM_Customer->set_default_address_id($id);
                if ($updated_record === false) {
                    $updated_record = true;
                }
            }
        }
        if ($updated_record === true) {
            return true;
        }
        return false;
    }
    /**
     * Set the address book entry as the primary address for the current customer
     *
     * @param int $id The ID of the address book entry
     * @access public
     * @return boolean
     */
    public static function set_primary_address($id)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Customer = Registry::get('Customer');
        if (is_numeric($id) && $id > 0) {
            $Qupdate = $OSCOM_PDO->prepare('update :table_customers set customers_default_address_id = :customers_default_address_id where customers_id = :customers_id');
            $Qupdate->bind_int(':customers_default_address_id', $id);
            $Qupdate->bind_int(':customers_id', $OSCOM_Customer->get_id());
            $Qupdate->execute();
            return $Qupdate->row_count() === 1;
        }
        return false;
    }
    /**
     * Delete an address book entry
     *
     * @param int $id The ID of the address book entry to delete
     * @access public
     * @return boolean
     */
    public static function delete_entry($id)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Customer = Registry::get('Customer');
        $Qdelete = $OSCOM_PDO->prepare('delete from :table_address_book where address_book_id = :address_book_id and customers_id = :customers_id');
        $Qdelete->bind_int(':address_book_id', $id);
        $Qdelete->bind_int(':customers_id', $OSCOM_Customer->get_id());
        $Qdelete->execute();
        return $Qdelete->row_count() === 1;
    }
}