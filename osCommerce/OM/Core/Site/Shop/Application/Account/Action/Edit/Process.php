<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Shop\Application\Account\Action\Edit;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Shop\Account;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $oscom_message_stack = Registry::get('MessageStack');
        $OSCOM_Customer = Registry::get('Customer');
        $data = [];
        if (ACCOUNT_GENDER >= 0) {
            if (isset($_POST['gender']) && ($_POST['gender'] == 'm' || $_POST['gender'] == 'f')) {
                $data['gender'] = $_POST['gender'];
            } else {
                $oscom_message_stack->add('Edit', OSCOM::get_def('field_customer_gender_error'));
            }
        }
        if (isset($_POST['firstname']) && strlen(trim($_POST['firstname'])) >= ACCOUNT_FIRST_NAME) {
            $data['firstname'] = $_POST['firstname'];
        } else {
            $oscom_message_stack->add('Edit', sprintf(OSCOM::get_def('field_customer_first_name_error'), ACCOUNT_FIRST_NAME));
        }
        if (isset($_POST['lastname']) && strlen(trim($_POST['lastname'])) >= ACCOUNT_LAST_NAME) {
            $data['lastname'] = $_POST['lastname'];
        } else {
            $oscom_message_stack->add('Edit', sprintf(OSCOM::get_def('field_customer_last_name_error'), ACCOUNT_LAST_NAME));
        }
        if (ACCOUNT_DATE_OF_BIRTH == '1') {
            if (isset($_POST['dob_days']) && isset($_POST['dob_months']) && isset($_POST['dob_years']) && checkdate($_POST['dob_months'], $_POST['dob_days'], $_POST['dob_years'])) {
                $data['dob'] = mktime(0, 0, 0, $_POST['dob_months'], $_POST['dob_days'], $_POST['dob_years']);
            } else {
                $oscom_message_stack->add('Edit', OSCOM::get_def('field_customer_date_of_birth_error'));
            }
        }
        if (isset($_POST['email_address']) && strlen(trim($_POST['email_address'])) >= ACCOUNT_EMAIL_ADDRESS) {
            if (filter_var($_POST['email_address'], FILTER_VALIDATE_EMAIL)) {
                if (Account::check_duplicate_entry($_POST['email_address']) === false) {
                    $data['email_address'] = $_POST['email_address'];
                } else {
                    $oscom_message_stack->add('Edit', OSCOM::get_def('field_customer_email_address_exists_error'));
                }
            } else {
                $oscom_message_stack->add('Edit', OSCOM::get_def('field_customer_email_address_check_error'));
            }
        } else {
            $oscom_message_stack->add('Edit', sprintf(OSCOM::get_def('field_customer_email_address_error'), ACCOUNT_EMAIL_ADDRESS));
        }
        if ($oscom_message_stack->size('Edit') === 0) {
            if (Account::save_entry($data)) {
                // reset the session variables
                if (ACCOUNT_GENDER > -1) {
                    $OSCOM_Customer->set_gender($data['gender']);
                }
                $OSCOM_Customer->set_first_name(trim($data['firstname']));
                $OSCOM_Customer->set_last_name(trim($data['lastname']));
                $OSCOM_Customer->set_email_address($data['email_address']);
                $oscom_message_stack->add('Account', OSCOM::get_def('success_account_updated'), 'success');
            }
            OSCOM::redirect(OSCOM::get_link(null, null, null, 'SSL'));
        }
    }
}