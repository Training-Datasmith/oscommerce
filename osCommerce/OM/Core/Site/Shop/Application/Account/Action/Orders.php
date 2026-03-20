<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Shop\Application\Account\Action;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Shop\Order;
class Orders
{
    public static function execute(Application_Abstract $application)
    {
        $OSCOM_Customer = Registry::get('Customer');
        $oscom_navigation_history = Registry::get('NavigationHistory');
        $OSCOM_Language = Registry::get('Language');
        $OSCOM_Service = Registry::get('Service');
        $OSCOM_Breadcrumb = Registry::get('Breadcrumb');
        if ($OSCOM_Customer->is_logged_on() === false) {
            $oscom_navigation_history->set_snapshot();
            OSCOM::redirect(OSCOM::get_link(null, null, 'LogIn', 'SSL'));
        }
        $application->set_page_title(OSCOM::get_def('orders_heading'));
        $application->set_page_content('orders.php');
        $OSCOM_Language->load('order');
        if ($OSCOM_Service->is_started('Breadcrumb')) {
            $OSCOM_Breadcrumb->add(OSCOM::get_def('breadcrumb_my_orders'), OSCOM::get_link(null, null, 'Orders', 'SSL'));
            if (is_numeric($_GET['Orders'])) {
                $OSCOM_Breadcrumb->add(sprintf(OSCOM::get_def('breadcrumb_order_information'), $_GET['Orders']), OSCOM::get_link(null, null, 'Orders=' . $_GET['Orders'], 'SSL'));
            }
        }
        if (is_numeric($_GET['Orders'])) {
            if (Order::get_customer_id($_GET['Orders']) !== $OSCOM_Customer->get_id()) {
                OSCOM::redirect(OSCOM::get_link(null, null, null, 'SSL'));
            }
            $application->set_page_title(sprintf(OSCOM::get_def('order_information_heading'), $_GET['Orders']));
            $application->set_page_content('orders_info.php');
        }
    }
}