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

class SubTotal extends \osCommerce\OM\Core\Site\Shop\OrderTotal
{
    public $output;

    public $_title;
    public $_code = 'SubTotal';
    public $_status = false;
    public $_sort_order;

    public function __construct()
    {
        $this->output = [];

        $this->_title = OSCOM::getDef('order_total_subtotal_title');
        $this->_description = OSCOM::getDef('order_total_subtotal_description');
        $this->_status = (defined('MODULE_ORDER_TOTAL_SUBTOTAL_STATUS') && (MODULE_ORDER_TOTAL_SUBTOTAL_STATUS == 'true') ? true : false);
        $this->_sort_order = (defined('MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER') ? MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER : null);
    }

    public function process()
    {
        $OSCOM_Currencies = Registry::get('Currencies');
        $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

        $this->output[] = ['title' => $this->_title . ':',
                                'text' => $OSCOM_Currencies->format($OSCOM_ShoppingCart->getSubTotal()),
                                'value' => $OSCOM_ShoppingCart->getSubTotal()];
    }
}
