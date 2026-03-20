<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin;

use Os_Commerce\OM\Core\Cache;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
class Order_Total extends \Os_Commerce\OM\Core\Site\Shop\Order_Total
{
    public $_group = 'order_total';
    public function has_keys()
    {
        return count($this->get_keys()) > 0;
    }
    public function install()
    {
        $OSCOM_Language = Registry::get('Language');
        $data = ['title' => $this->_title, 'code' => $this->_code, 'author_name' => $this->_author_name, 'author_www' => $this->_author_www, 'group' => 'OrderTotal'];
        OSCOM::call_db('Admin\InsertModule', $data, 'Site');
        foreach ($OSCOM_Language->get_all() as $key => $value) {
            if (file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages/' . $key . '/modules/order_total/' . $this->_code . '.xml')) {
                foreach ($OSCOM_Language->extract_definitions($key . '/modules/order_total/' . $this->_code . '.xml') as $def) {
                    $def['id'] = $value['id'];
                    OSCOM::call_db('Admin\InsertLanguageDefinition', $def, 'Site');
                }
            }
        }
        Cache::clear('languages');
    }
    public function remove()
    {
        $OSCOM_Language = Registry::get('Language');
        $data = ['code' => $this->_code, 'group' => 'OrderTotal'];
        OSCOM::call_db('Admin\DeleteModule', $data, 'Site');
        if ($this->has_keys()) {
            OSCOM::call_db('Admin\DeleteConfigurationParameters', $this->get_keys(), 'Site');
            Cache::clear('configuration');
        }
        if (file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages/' . $OSCOM_Language->get_code() . '/modules/order_total/' . $this->_code . '.xml')) {
            foreach ($OSCOM_Language->extract_definitions($OSCOM_Language->get_code() . '/modules/order_total/' . $this->_code . '.xml') as $def) {
                OSCOM::call_db('Admin\DeleteLanguageDefinitions', $def, 'Site');
            }
            Cache::clear('languages');
        }
    }
}