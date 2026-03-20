<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Core_Update\Action\Apply;

use Os_Commerce\OM\Core\Access;
use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\Site\Admin\Application\Core_Update\Core_Update;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        if (!Core_Update::local_package_exists() || Core_Update::get_package_info('version_from') != OSCOM::get_version()) {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_wrong_version_to_update_from'), 'error');
            OSCOM::redirect(OSCOM::get_link());
        }
        if (Core_Update::can_apply_package()) {
            if (Core_Update::apply_package()) {
                Core_Update::delete_package();
                // Refresh access list for new/deleted Applications
                $_SESSION[OSCOM::get_site()]['access'] = Access::get_user_levels($_SESSION[OSCOM::get_site()]['id']);
                Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_success_action_performed'), 'success');
            } else {
                Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_action_not_performed'), 'error');
            }
        } else {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_check_target_permissions'), 'error');
            OSCOM::redirect(OSCOM::get_link(null, null, 'Apply&v=' . $_GET['v']));
        }
        OSCOM::redirect(OSCOM::get_link());
    }
}