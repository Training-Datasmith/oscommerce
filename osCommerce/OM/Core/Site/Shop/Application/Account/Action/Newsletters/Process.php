<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Shop\Application\Account\Action\Newsletters;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Customer = Registry::get('Customer');
        $oscom_message_stack = Registry::get('MessageStack');
        if (isset($_POST['newsletter_general']) && is_numeric($_POST['newsletter_general'])) {
            $newsletter_general = (int) $_POST['newsletter_general'];
        } else {
            $newsletter_general = 0;
        }
        // HPDL Should be moved to the customers class!
        $Qnewsletter = $OSCOM_PDO->prepare('select customers_newsletter from :table_customers where customers_id = :customers_id');
        $Qnewsletter->bind_int(':customers_id', $OSCOM_Customer->get_id());
        $Qnewsletter->execute();
        if ($newsletter_general !== $Qnewsletter->value_int('customers_newsletter')) {
            $newsletter_general = $Qnewsletter->value('customers_newsletter') == '1' ? '0' : '1';
            $Qupdate = $OSCOM_PDO->prepare('update :table_customers set customers_newsletter = :customers_newsletter where customers_id = :customers_id');
            $Qupdate->bind_int(':customers_newsletter', $newsletter_general);
            $Qupdate->bind_int(':customers_id', $OSCOM_Customer->get_id());
            $Qupdate->execute();
            if ($Qupdate->row_count() === 1) {
                $oscom_message_stack->add('Account', OSCOM::get_def('success_newsletter_updated'), 'success');
            }
        }
        OSCOM::redirect(OSCOM::get_link(null, null, null, 'SSL'));
    }
}