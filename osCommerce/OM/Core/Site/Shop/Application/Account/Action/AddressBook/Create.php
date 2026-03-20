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
class Create
{
    public static function execute(Application_Abstract $application)
    {
        $OSCOM_Service = Registry::get('Service');
        $OSCOM_Breadcrumb = Registry::get('Breadcrumb');
        $OSCOM_Template = Registry::get('Template');
        $oscom_message_stack = Registry::get('MessageStack');
        if ($OSCOM_Service->is_started('Breadcrumb')) {
            $OSCOM_Breadcrumb->add(OSCOM::get_def('breadcrumb_address_book_add_entry'), OSCOM::get_link(null, null, 'AddressBook&Create', 'SSL'));
        }
        $application->set_page_title(OSCOM::get_def('address_book_add_entry_heading'));
        $application->set_page_content('address_book_process.php');
        $OSCOM_Template->add_javascript_php_filename(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/assets/form_check.js.php');
        if (Address_Book::number_of_entries() >= MAX_ADDRESS_BOOK_ENTRIES) {
            $oscom_message_stack->add('AddressBook', OSCOM::get_def('error_address_book_full'));
            $application->set_page_title(OSCOM::get_def('address_book_heading'));
            $application->set_page_content('address_book.php');
            return true;
        }
    }
}