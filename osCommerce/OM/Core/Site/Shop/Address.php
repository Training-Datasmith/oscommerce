<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Shop;

use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\Registry;
/**
 * The Address class handles address related functions such as the format and country and zone information
 */
class Address
{
    /**
     * Correctly format an address to the address format rule assigned to its country
     *
     * @param array $address An array (or address_book ID) containing the address information
     * @param string $new_line The string to break new lines with
     * @access public
     * @return string
     */
    public static function format($address, $new_line = null)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $address_format = '';
        if (is_numeric($address)) {
            $Qaddress = $OSCOM_PDO->prepare('select ab.entry_firstname as firstname, ab.entry_lastname as lastname, ab.entry_company as company, ab.entry_street_address as street_address, ab.entry_suburb as suburb, ab.entry_city as city, ab.entry_postcode as postcode, ab.entry_state as state, ab.entry_zone_id as zone_id, ab.entry_country_id as country_id, z.zone_code as zone_code, c.countries_name as country_title from :table_address_book ab left join :table_zones z on (ab.entry_zone_id = z.zone_id), :table_countries c where ab.address_book_id = :address_book_id and ab.entry_country_id = c.countries_id');
            $Qaddress->bind_int(':address_book_id', $address);
            $Qaddress->execute();
            $address = $Qaddress->fetch();
        }
        $firstname = $lastname = '';
        if (isset($address['firstname']) && !empty($address['firstname'])) {
            $firstname = $address['firstname'];
            $lastname = $address['lastname'];
        } elseif (isset($address['name']) && !empty($address['name'])) {
            $firstname = $address['name'];
        }
        $state = $address['state'];
        $state_code = $address['zone_code'];
        if (isset($address['zone_id']) && is_numeric($address['zone_id']) && $address['zone_id'] > 0) {
            $state = self::get_zone_name($address['zone_id']);
            $state_code = self::get_zone_code($address['zone_id']);
        }
        $country = $address['country_title'];
        if (empty($country) && isset($address['country_id']) && is_numeric($address['country_id']) && $address['country_id'] > 0) {
            $country = self::get_country_name($address['country_id']);
        }
        if (isset($address['format'])) {
            $address_format = $address['format'];
        } elseif (isset($address['country_id']) && is_numeric($address['country_id']) && $address['country_id'] > 0) {
            $address_format = self::get_format($address['country_id']);
        }
        if (empty($address_format)) {
            $address_format = ":name\n:street_address\n:postcode :city\n:country";
        }
        $find_array = ['/\:name\b/', '/\:street_address\b/', '/\:suburb\b/', '/\:city\b/', '/\:postcode\b/', '/\:state\b/', '/\:state_code\b/', '/\:country\b/'];
        $replace_array = [HTML::output_protected($firstname . ' ' . $lastname), HTML::output_protected($address['street_address']), HTML::output_protected($address['suburb']), HTML::output_protected($address['city']), HTML::output_protected($address['postcode']), HTML::output_protected($state), HTML::output_protected($state_code), HTML::output_protected($country)];
        $formated = preg_replace($find_array, $replace_array, $address_format);
        if (ACCOUNT_COMPANY > -1 && !empty($address['company'])) {
            $formated = HTML::output_protected($address['company']) . "\n" . $formated;
        }
        if (!empty($new_line)) {
            $formated = str_replace("\n", $new_line, $formated);
        }
        return $formated;
    }
    /**
     * Return all countries in an array
     *
     * @access public
     * @return array
     */
    public static function get_countries()
    {
        $OSCOM_PDO = Registry::get('PDO');
        static $countries;
        if (!isset($countries)) {
            $countries = [];
            $Qcountries = $OSCOM_PDO->query('select * from :table_countries order by countries_name');
            $Qcountries->execute();
            while ($Qcountries->fetch()) {
                $countries[] = ['id' => $Qcountries->value_int('countries_id'), 'name' => $Qcountries->value('countries_name'), 'iso_2' => $Qcountries->value('countries_iso_code_2'), 'iso_3' => $Qcountries->value('countries_iso_code_3'), 'format' => $Qcountries->value('address_format')];
            }
        }
        return $countries;
    }
    /**
     * Return the country name
     *
     * @param int $id The ID of the country
     * @access public
     * @return string
     */
    public static function get_country_name($id)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qcountry = $OSCOM_PDO->prepare('select countries_name from :table_countries where countries_id = :countries_id');
        $Qcountry->bind_int(':countries_id', $id);
        $Qcountry->execute();
        return $Qcountry->value('countries_name');
    }
    /**
     * Return the country 2 character ISO code
     *
     * @param int $id The ID of the country
     * @access public
     * @return string
     */
    public static function get_country_iso_code2($id)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qcountry = $OSCOM_PDO->prepare('select countries_iso_code_2 from :table_countries where countries_id = :countries_id');
        $Qcountry->bind_int(':countries_id', $id);
        $Qcountry->execute();
        return $Qcountry->value('countries_iso_code_2');
    }
    /**
     * Return the country 3 character ISO code
     *
     * @param int $id The ID of the country
     * @access public
     * @return string
     */
    public static function get_country_iso_code3($id)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qcountry = $OSCOM_PDO->prepare('select countries_iso_code_3 from :table_countries where countries_id = :countries_id');
        $Qcountry->bind_int(':countries_id', $id);
        $Qcountry->execute();
        return $Qcountry->value('countries_iso_code_3');
    }
    /**
     * Return the address format rule for the country
     *
     * @param int $id The ID of the country
     * @access public
     * @return string
     */
    public static function get_format($id)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qcountry = $OSCOM_PDO->prepare('select address_format from :table_countries where countries_id = :countries_id');
        $Qcountry->bind_int(':countries_id', $id);
        $Qcountry->execute();
        return $Qcountry->value('address_format');
    }
    /**
     * Return the zone name
     *
     * @param int $id The ID of the zone
     * @access public
     * @return string
     */
    public static function get_zone_name($id)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qzone = $OSCOM_PDO->prepare('select zone_name from :table_zones where zone_id = :zone_id');
        $Qzone->bind_int(':zone_id', $id);
        $Qzone->execute();
        return $Qzone->value('zone_name');
    }
    /**
     * Return the zone code
     *
     * @param int $id The ID of the zone
     * @access public
     * @return string
     */
    public static function get_zone_code($id)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qzone = $OSCOM_PDO->prepare('select zone_code from :table_zones where zone_id = :zone_id');
        $Qzone->bind_int(':zone_id', $id);
        $Qzone->execute();
        return $Qzone->value('zone_code');
    }
    /**
     * Check if a country has zones
     *
     * @param int $id The ID of the country
     * @access public
     * @return boolean
     * @since v3.0.2
     */
    public static function has_zones($id)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qzones = $OSCOM_PDO->prepare('select zone_id from :table_zones where zone_country_id = :zone_country_id limit 1');
        $Qzones->bind_int(':zone_country_id', $id);
        $Qzones->execute();
        return $Qzones->fetch() !== false;
    }
    /**
     * Return the zones belonging to a country, or all zones
     *
     * @param int $id The ID of the country
     * @access public
     * @return array
     */
    public static function get_zones($id = null)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $zones_array = [];
        $sql_query = 'select z.zone_id, z.zone_country_id, z.zone_name, c.countries_name from :table_zones z, :table_countries c where';
        if (!empty($id)) {
            $sql_query .= ' z.zone_country_id = :zone_country_id and';
        }
        $sql_query .= ' z.zone_country_id = c.countries_id order by c.countries_name, z.zone_name';
        if (!empty($id)) {
            $Qzones = $OSCOM_PDO->prepare($sql_query);
            $Qzones->bind_int(':zone_country_id', $id);
        } else {
            $Qzones = $OSCOM_PDO->query($sql_query);
        }
        $Qzones->execute();
        while ($Qzones->fetch()) {
            $zones_array[] = ['id' => $Qzones->value_int('zone_id'), 'name' => $Qzones->value('zone_name'), 'country_id' => $Qzones->value_int('zone_country_id'), 'country_name' => $Qzones->value('countries_name')];
        }
        return $zones_array;
    }
}