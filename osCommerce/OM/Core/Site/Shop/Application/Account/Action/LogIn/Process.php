<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Shop\Application\Account\Action\Log_In;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Shop\Account;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $oscom_navigation_history = Registry::get('NavigationHistory');
        $oscom_message_stack = Registry::get('MessageStack');
        if (!empty($_POST['email_address']) && !empty($_POST['password']) && Account::log_in($_POST['email_address'], $_POST['password'])) {
            $oscom_navigation_history->remove_current_page();
            if ($oscom_navigation_history->has_snapshot()) {
                $oscom_navigation_history->redirect_to_snapshot();
            } else {
                OSCOM::redirect(OSCOM::get_link(null, OSCOM::get_default_site_application(), null, 'AUTO'));
            }
        }
        $oscom_message_stack->add('LogIn', OSCOM::get_def('error_login_no_match'));
    }
}