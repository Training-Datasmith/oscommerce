<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Module\Order_Total;

use Os_Commerce\OM\Core\OSCOM;
class Shipping extends \Os_Commerce\OM\Core\Site\Admin\Order_Total
{
    public $_title;
    public $_code = 'Shipping';
    public $_author_name = 'osCommerce';
    public $_author_www = 'http://www.oscommerce.com';
    public $_status = false;
    public $_sort_order;
    public function __construct()
    {
        $this->_title = OSCOM::get_def('order_total_shipping_title');
        $this->_description = OSCOM::get_def('order_total_shipping_description');
        $this->_status = defined('MODULE_ORDER_TOTAL_SHIPPING_STATUS') && MODULE_ORDER_TOTAL_SHIPPING_STATUS == 'true' ? true : false;
        $this->_sort_order = defined('MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER') ? MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER : null;
    }
    public function is_installed()
    {
        return defined('MODULE_ORDER_TOTAL_SHIPPING_STATUS');
    }
    public function install()
    {
        parent::install();
        $data = [['title' => 'Display Shipping', 'key' => 'MODULE_ORDER_TOTAL_SHIPPING_STATUS', 'value' => 'true', 'description' => 'Do you want to display the order shipping cost?', 'group_id' => '6', 'set_function' => 'osc_cfg_set_boolean_value(array(\'true\', \'false\'))'], ['title' => 'Sort Order', 'key' => 'MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER', 'value' => '2', 'description' => 'Sort order of display.', 'group_id' => '6']];
        OSCOM::call_db('Admin\InsertConfigurationParameters', $data, 'Site');
    }
    public function get_keys()
    {
        return ['MODULE_ORDER_TOTAL_SHIPPING_STATUS', 'MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER'];
    }
}