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
class Save_Address
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        if (!isset($data['id'])) {
            $data['id'] = null;
        }
        $error = false;
        $OSCOM_PDO->begin_transaction();
        if (isset($data['id']) && is_numeric($data['id'])) {
            $Qab = $OSCOM_PDO->prepare('update :table_address_book set entry_gender = :entry_gender, entry_company = :entry_company, entry_firstname = :entry_firstname, entry_lastname = :entry_lastname, entry_street_address = :entry_street_address, entry_suburb = :entry_suburb, entry_postcode = :entry_postcode, entry_city = :entry_city, entry_state = :entry_state, entry_country_id = :entry_country_id, entry_zone_id = :entry_zone_id, entry_telephone = :entry_telephone, entry_fax = :entry_fax where address_book_id = :address_book_id and customers_id = :customers_id');
            $Qab->bind_int(':address_book_id', $data['id']);
        } else {
            $Qab = $OSCOM_PDO->prepare('insert into :table_address_book (customers_id, entry_gender, entry_company, entry_firstname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_country_id, entry_zone_id, entry_telephone, entry_fax) values (:customers_id, :entry_gender, :entry_company, :entry_firstname, :entry_lastname, :entry_street_address, :entry_suburb, :entry_postcode, :entry_city, :entry_state, :entry_country_id, :entry_zone_id, :entry_telephone, :entry_fax)');
        }
        $Qab->bind_int(':customers_id', $data['customer_id']);
        $Qab->bind_value(':entry_gender', $data['gender']);
        $Qab->bind_value(':entry_company', $data['company']);
        $Qab->bind_value(':entry_firstname', $data['firstname']);
        $Qab->bind_value(':entry_lastname', $data['lastname']);
        $Qab->bind_value(':entry_street_address', $data['street_address']);
        $Qab->bind_value(':entry_suburb', $data['suburb']);
        $Qab->bind_value(':entry_postcode', $data['postcode']);
        $Qab->bind_value(':entry_city', $data['city']);
        $Qab->bind_value(':entry_state', $data['state']);
        $Qab->bind_int(':entry_country_id', $data['country_id']);
        if (is_numeric($data['zone_id'])) {
            $Qab->bind_int(':entry_zone_id', $data['zone_id']);
        } else {
            $Qab->bind_null(':entry_zone_id');
        }
        $Qab->bind_value(':entry_telephone', $data['telephone']);
        $Qab->bind_value(':entry_fax', $data['fax']);
        $Qab->execute();
        if (!$Qab->is_error()) {
            if (isset($data['default']) && $data['default'] === true) {
                $address_book_id = isset($data['id']) && is_numeric($data['id']) ? $data['id'] : $OSCOM_PDO->last_insert_id();
                $Qupdate = $OSCOM_PDO->prepare('update :table_customers set customers_default_address_id = :customers_default_address_id where customers_id = :customers_id');
                $Qupdate->bind_int(':customers_default_address_id', $address_book_id);
                $Qupdate->bind_int(':customers_id', $data['customer_id']);
                $Qupdate->execute();
                if ($Qupdate->is_error()) {
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