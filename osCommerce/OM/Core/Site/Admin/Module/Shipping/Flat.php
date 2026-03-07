<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Admin\Module\Shipping;

use osCommerce\OM\Core\OSCOM;

class Flat extends \osCommerce\OM\Core\Site\Admin\Shipping
{
    public $icon;

    public $_title;
    public $_code = 'Flat';
    public $_author_name = 'osCommerce';
    public $_author_www = 'http://www.oscommerce.com';
    public $_status = false;
    public $_sort_order;

    public function __construct()
    {
        $this->icon = '';

        $this->_title = OSCOM::getDef('shipping_flat_title');
        $this->_description = OSCOM::getDef('shipping_flat_description');
        $this->_status = (defined('MODULE_SHIPPING_FLAT_STATUS') && (MODULE_SHIPPING_FLAT_STATUS == 'True') ? true : false);
        $this->_sort_order = (defined('MODULE_SHIPPING_FLAT_SORT_ORDER') ? MODULE_SHIPPING_FLAT_SORT_ORDER : null);
    }

    public function isInstalled()
    {
        return defined('MODULE_SHIPPING_FLAT_STATUS');
    }

    public function install()
    {
        parent::install();

        $data = [['title' => 'Enable Flat Shipping',
                            'key' => 'MODULE_SHIPPING_FLAT_STATUS',
                            'value' => 'True',
                            'description' => 'Do you want to offer flat rate shipping?',
                            'group_id' => '6',
                            'set_function' => 'osc_cfg_set_boolean_value(array(\'True\', \'False\'))'],
                      ['title' => 'Shipping Cost',
                            'key' => 'MODULE_SHIPPING_FLAT_COST',
                            'value' => '5.00',
                            'description' => 'The shipping cost for all orders using this shipping method.',
                            'group_id' => '6'],
                      ['title' => 'Tax Class',
                            'key' => 'MODULE_SHIPPING_FLAT_TAX_CLASS',
                            'value' => '0',
                            'description' => 'Use the following tax class on the shipping fee.',
                            'group_id' => '6',
                            'use_function' => 'osc_cfg_use_get_tax_class_title',
                            'set_function' => 'osc_cfg_set_tax_classes_pull_down_menu'],
                      ['title' => 'Shipping Zone',
                            'key' => 'MODULE_SHIPPING_FLAT_ZONE',
                            'value' => '0',
                            'description' => 'If a zone is selected, only enable this shipping method for that zone.',
                            'group_id' => '6',
                            'use_function' => 'osc_cfg_use_get_zone_class_title',
                            'set_function' => 'osc_cfg_set_zone_classes_pull_down_menu'],
                      ['title' => 'Sort Order',
                            'key' => 'MODULE_SHIPPING_FLAT_SORT_ORDER',
                            'value' => '0',
                            'description' => 'Sort order of display.',
                            'group_id' => '6'],
                     ];

        OSCOM::callDB('Admin\InsertConfigurationParameters', $data, 'Site');
    }

    public function getKeys()
    {
        return ['MODULE_SHIPPING_FLAT_STATUS',
                     'MODULE_SHIPPING_FLAT_COST',
                     'MODULE_SHIPPING_FLAT_TAX_CLASS',
                     'MODULE_SHIPPING_FLAT_ZONE',
                     'MODULE_SHIPPING_FLAT_SORT_ORDER'];
    }
}
