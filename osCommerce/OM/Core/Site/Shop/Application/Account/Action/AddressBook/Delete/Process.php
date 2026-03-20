<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Shop\Application\Account\Action\Address_Book\Delete;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Shop\Address_Book;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $oscom_message_stack = Registry::get('MessageStack');
        if (Address_Book::delete_entry($_GET['Delete'])) {
            $oscom_message_stack->add('AddressBook', OSCOM::get_def('success_address_book_entry_deleted'), 'success');
        }
        OSCOM::redirect(OSCOM::get_link(null, null, 'AddressBook', 'SSL'));
    }
}