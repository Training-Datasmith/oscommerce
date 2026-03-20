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
class Log_In
{
    public static function execute(Application_Abstract $application)
    {
        $OSCOM_Session = Registry::get('Session');
        $OSCOM_Service = Registry::get('Service');
        $OSCOM_Breadcrumb = Registry::get('Breadcrumb');
        // redirect the customer to a friendly cookies-must-be-enabled page if cookies
        // are disabled (or the session has not started)
        if ($OSCOM_Session->has_started() === false) {
            OSCOM::redirect(OSCOM::get_link(null, 'Info', 'Cookies'));
        }
        $application->set_page_title(OSCOM::get_def('sign_in_heading'));
        $application->set_page_content('login.php');
        if ($OSCOM_Service->is_started('Breadcrumb')) {
            $OSCOM_Breadcrumb->add(OSCOM::get_def('breadcrumb_sign_in'), OSCOM::get_link(null, null, 'LogIn', 'SSL'));
        }
    }
}