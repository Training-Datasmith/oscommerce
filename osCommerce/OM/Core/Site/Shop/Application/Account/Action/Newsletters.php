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
class Newsletters
{
    public static function execute(Application_Abstract $application)
    {
        $OSCOM_Customer = Registry::get('Customer');
        $oscom_navigation_history = Registry::get('NavigationHistory');
        $OSCOM_Service = Registry::get('Service');
        $OSCOM_Breadcrumb = Registry::get('Breadcrumb');
        if ($OSCOM_Customer->is_logged_on() === false) {
            $oscom_navigation_history->set_snapshot();
            OSCOM::redirect(OSCOM::get_link(null, null, 'LogIn', 'SSL'));
        }
        $application->set_page_title(OSCOM::get_def('newsletters_heading'));
        $application->set_page_content('newsletters.php');
        if ($OSCOM_Service->is_started('Breadcrumb')) {
            $OSCOM_Breadcrumb->add(OSCOM::get_def('breadcrumb_newsletters'), OSCOM::get_link(null, null, 'Newsletters', 'SSL'));
        }
    }
}