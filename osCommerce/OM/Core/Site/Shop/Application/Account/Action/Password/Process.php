<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Shop\Application\Account\Action\Password;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Shop\Account;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $oscom_message_stack = Registry::get('MessageStack');
        if (!isset($_POST['password_current']) || strlen(trim($_POST['password_current'])) < ACCOUNT_PASSWORD) {
            $oscom_message_stack->add('Password', sprintf(OSCOM::get_def('field_customer_password_current_error'), ACCOUNT_PASSWORD));
        } elseif (!isset($_POST['password_new']) || strlen(trim($_POST['password_new'])) < ACCOUNT_PASSWORD) {
            $oscom_message_stack->add('Password', sprintf(OSCOM::get_def('field_customer_password_new_error'), ACCOUNT_PASSWORD));
        } elseif (!isset($_POST['password_confirmation']) || trim($_POST['password_new']) != trim($_POST['password_confirmation'])) {
            $oscom_message_stack->add('Password', OSCOM::get_def('field_customer_password_new_mismatch_with_confirmation_error'));
        }
        if ($oscom_message_stack->size('Password') === 0) {
            if (Account::check_password(trim($_POST['password_current']))) {
                if (Account::save_password(trim($_POST['password_new']))) {
                    $oscom_message_stack->add('Account', OSCOM::get_def('success_password_updated'), 'success');
                    OSCOM::redirect(OSCOM::get_link(null, null, null, 'SSL'));
                } else {
                    $oscom_message_stack->add('Password', sprintf(OSCOM::get_def('field_customer_password_new_error'), ACCOUNT_PASSWORD));
                }
            } else {
                $oscom_message_stack->add('Password', OSCOM::get_def('error_current_password_not_matching'));
            }
        }
    }
}