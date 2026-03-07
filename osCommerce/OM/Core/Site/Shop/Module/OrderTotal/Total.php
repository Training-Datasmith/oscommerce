<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Shop\Module\OrderTotal;

use osCommerce\OM\Core\OSCOM;
use osCommerce\OM\Core\Registry;

class Total extends \osCommerce\OM\Core\Site\Shop\OrderTotal
{
    public $output;

    public $_title;
    public $_code = 'Total';
    public $_status = false;
    public $_sort_order;

    public function __construct()
    {
        $this->output = [];

        $this->_title = OSCOM::getDef('order_total_total_title');
        $this->_description = OSCOM::getDef('order_total_total_description');
        $this->_status = (defined('MODULE_ORDER_TOTAL_TOTAL_STATUS') && (MODULE_ORDER_TOTAL_TOTAL_STATUS == 'true') ? true : false);
        $this->_sort_order = (defined('MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER') ? MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER : null);
    }

    public function process()
    {
        $OSCOM_Currencies = Registry::get('Currencies');
        $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

        $this->output[] = ['title' => $this->_title . ':',
                                'text' => '<b>' . $OSCOM_Currencies->format($OSCOM_ShoppingCart->getTotal()) . '</b>',
                                'value' => $OSCOM_ShoppingCart->getTotal()];
    }
}
