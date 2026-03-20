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
class Log_Off
{
    public static function execute(Application_Abstract $application)
    {
        $OSCOM_Service = Registry::get('Service');
        $OSCOM_Breadcrumb = Registry::get('Breadcrumb');
        $oscom_shopping_cart = Registry::get('ShoppingCart');
        $OSCOM_Customer = Registry::get('Customer');
        $application->set_page_title(OSCOM::get_def('sign_out_heading'));
        $application->set_page_content('logoff.php');
        if ($OSCOM_Service->is_started('Breadcrumb')) {
            $OSCOM_Breadcrumb->add(OSCOM::get_def('breadcrumb_sign_out'));
        }
        $OSCOM_Customer->reset();
        $oscom_shopping_cart->reset();
    }
}