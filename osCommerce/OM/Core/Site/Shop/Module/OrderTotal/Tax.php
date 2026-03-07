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

class Tax extends \osCommerce\OM\Core\Site\Shop\OrderTotal
{
    public $output;

    public $_title;
    public $_code = 'Tax';
    public $_status = false;
    public $_sort_order;

    public function __construct()
    {
        $this->output = [];

        $this->_title = OSCOM::getDef('order_total_tax_title');
        $this->_description = OSCOM::getDef('order_total_tax_description');
        $this->_status = (defined('MODULE_ORDER_TOTAL_TAX_STATUS') && (MODULE_ORDER_TOTAL_TAX_STATUS == 'true') ? true : false);
        $this->_sort_order = (defined('MODULE_ORDER_TOTAL_TAX_SORT_ORDER') ? MODULE_ORDER_TOTAL_TAX_SORT_ORDER : null);
    }

    public function process()
    {
        $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
        $OSCOM_Currencies = Registry::get('Currencies');

        foreach ($OSCOM_ShoppingCart->getTaxGroups() as $key => $value) {
            if ($value > 0) {
                if (DISPLAY_PRICE_WITH_TAX == '1') {
                    $OSCOM_ShoppingCart->addToTotal($value);
                }

                $this->output[] = ['title' => $key . ':',
                                        'text' => $OSCOM_Currencies->format($value),
                                        'value' => $value];
            }
        }
    }
}
