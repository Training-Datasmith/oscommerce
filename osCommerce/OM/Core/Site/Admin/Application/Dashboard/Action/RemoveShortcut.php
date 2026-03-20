<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Dashboard\Action;

use Os_Commerce\OM\Core\Access;
use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Dashboard\Dashboard;
class Remove_Shortcut
{
    public static function execute(Application_Abstract $application)
    {
        if (!empty($_GET['shortcut'])) {
            $application = HTML::sanitize($_GET['shortcut']);
            if (OSCOM::site_application_exists($application)) {
                if (Dashboard::delete_shortcut($_SESSION[OSCOM::get_site()]['id'], $application)) {
                    $_SESSION[OSCOM::get_site()]['access'] = Access::get_user_levels($_SESSION[OSCOM::get_site()]['id']);
                    Registry::get('MessageStack')->add('header', OSCOM::get_def('ms_success_shortcut_removed'), 'success');
                    OSCOM::redirect(OSCOM::get_link(null, $application));
                }
            }
        }
        OSCOM::redirect(OSCOM::get_link());
    }
}