<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Shop\Application\Account\Action\Address_Book;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Shop\Address_Book;
class Delete
{
    public static function execute(Application_Abstract $application)
    {
        $OSCOM_Customer = Registry::get('Customer');
        $oscom_message_stack = Registry::get('MessageStack');
        $OSCOM_Service = Registry::get('Service');
        $OSCOM_Breadcrumb = Registry::get('Breadcrumb');
        if ($_GET['Delete'] == $OSCOM_Customer->get_default_address_id()) {
            $oscom_message_stack->add('AddressBook', OSCOM::get_def('warning_primary_address_deletion'), 'warning');
        } else if (Address_Book::check_entry($_GET['Delete']) === false) {
            $oscom_message_stack->add('AddressBook', OSCOM::get_def('error_address_book_entry_non_existing'), 'error');
        }
        if ($oscom_message_stack->size('AddressBook') > 0) {
            OSCOM::redirect(OSCOM::get_link(null, null, 'AddressBook', 'SSL'));
        }
        if ($OSCOM_Service->is_started('Breadcrumb')) {
            $OSCOM_Breadcrumb->add(OSCOM::get_def('breadcrumb_address_book_delete_entry'), OSCOM::get_link(null, null, 'AddressBook&Delete=' . $_GET['Delete'], 'SSL'));
        }
        $application->set_page_title(OSCOM::get_def('address_book_delete_entry_heading'));
        $application->set_page_content('address_book_delete.php');
    }
}