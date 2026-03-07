<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Admin\Module\OrderTotal;

use osCommerce\OM\Core\OSCOM;

class Total extends \osCommerce\OM\Core\Site\Admin\OrderTotal
{
    public $_title;
    public $_code = 'Total';
    public $_author_name = 'osCommerce';
    public $_author_www = 'http://www.oscommerce.com';
    public $_status = false;
    public $_sort_order;

    public function __construct()
    {
        $this->_title = OSCOM::getDef('order_total_total_title');
        $this->_description = OSCOM::getDef('order_total_total_description');
        $this->_status = (defined('MODULE_ORDER_TOTAL_TOTAL_STATUS') && (MODULE_ORDER_TOTAL_TOTAL_STATUS == 'true') ? true : false);
        $this->_sort_order = (defined('MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER') ? MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER : null);
    }

    public function isInstalled()
    {
        return defined('MODULE_ORDER_TOTAL_TOTAL_STATUS');
    }

    public function install()
    {
        parent::install();

        $data = [['title' => 'Display Total',
                            'key' => 'MODULE_ORDER_TOTAL_TOTAL_STATUS',
                            'value' => 'true',
                            'description' => 'Do you want to display the total order value?',
                            'group_id' => '6',
                            'set_function' => 'osc_cfg_set_boolean_value(array(\'true\', \'false\'))'],
                      ['title' => 'Sort Order',
                            'key' => 'MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER',
                            'value' => '4',
                            'description' => 'Sort order of display.',
                            'group_id' => '6'],
                     ];

        OSCOM::callDB('Admin\InsertConfigurationParameters', $data, 'Site');
    }

    public function getKeys()
    {
        return ['MODULE_ORDER_TOTAL_TOTAL_STATUS',
                     'MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER'];
    }
}
