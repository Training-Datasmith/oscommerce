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
class Password_Forgotten
{
    public static function execute(Application_Abstract $application)
    {
        $OSCOM_Template = Registry::get('Template');
        $OSCOM_Service = Registry::get('Service');
        $OSCOM_Breadcrumb = Registry::get('Breadcrumb');
        $application->set_page_title(OSCOM::get_def('password_forgotten_heading'));
        $application->set_page_content('password_forgotten.php');
        $OSCOM_Template->add_javascript_php_filename(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/assets/form_check.js.php');
        if ($OSCOM_Service->is_started('Breadcrumb')) {
            $OSCOM_Breadcrumb->add(OSCOM::get_def('breadcrumb_password_forgotten'), OSCOM::get_link(null, null, 'PasswordForgotten', 'SSL'));
        }
    }
}