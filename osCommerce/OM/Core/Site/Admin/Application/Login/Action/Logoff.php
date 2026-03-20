<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Login\Action;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
class Logoff
{
    public static function execute(Application_Abstract $application)
    {
        unset($_SESSION[OSCOM::get_site()]);
        Registry::get('MessageStack')->add('header', OSCOM::get_def('ms_success_logged_out'), 'success');
        OSCOM::redirect(OSCOM::get_link(null, OSCOM::get_default_site_application()));
    }
}