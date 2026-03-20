<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Shop\Application\Account\Action\Password_Forgotten;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\Hash;
use Os_Commerce\OM\Core\Mail;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Shop\Account;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $oscom_message_stack = Registry::get('MessageStack');
        $Qcheck = $OSCOM_PDO->prepare('select customers_id, customers_firstname, customers_lastname, customers_gender, customers_email_address, customers_password from :table_customers where customers_email_address = :customers_email_address limit 1');
        $Qcheck->bind_value(':customers_email_address', $_POST['email_address']);
        $Qcheck->execute();
        if ($Qcheck->fetch() !== false) {
            $password = Hash::get_random_string(ACCOUNT_PASSWORD);
            if (Account::save_password($password, $Qcheck->value_int('customers_id'))) {
                if (ACCOUNT_GENDER > -1) {
                    if ($Qcheck->value('customers_gender') == 'm') {
                        $email_text = sprintf(OSCOM::get_def('email_addressing_gender_male'), $Qcheck->value_protected('customers_lastname')) . "\n\n";
                    } else {
                        $email_text = sprintf(OSCOM::get_def('email_addressing_gender_female'), $Qcheck->value_protected('customers_lastname')) . "\n\n";
                    }
                } else {
                    $email_text = sprintf(OSCOM::get_def('email_addressing_gender_unknown'), $Qcheck->value_protected('customers_firstname') . ' ' . $Qcheck->value_protected('customers_lastname')) . "\n\n";
                }
                $email_text .= sprintf(OSCOM::get_def('email_password_reminder_body'), OSCOM::get_ip_address(), STORE_NAME, $password, STORE_OWNER_EMAIL_ADDRESS);
                $p_email = new Mail($Qcheck->value_protected('customers_firstname') . ' ' . $Qcheck->value_protected('customers_lastname'), $Qcheck->value_protected('customers_email_address'), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, sprintf(OSCOM::get_def('email_password_reminder_subject'), STORE_NAME));
                $p_email->set_body_plain($email_text);
                $p_email->send();
                $oscom_message_stack->add('LogIn', OSCOM::get_def('success_password_forgotten_sent'), 'success');
            }
            OSCOM::redirect(OSCOM::get_link(null, null, 'LogIn', 'SSL'));
        } else {
            $oscom_message_stack->add('PasswordForgotten', OSCOM::get_def('error_password_forgotten_no_email_address_found'));
        }
    }
}