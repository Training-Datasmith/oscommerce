<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Core_Update\Action;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Core_Update\Core_Update;
class Apply
{
    public static function execute(Application_Abstract $application)
    {
        if (!isset($_GET['v']) || !Core_Update::package_exists($_GET['v'])) {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_select_version_to_view'), 'error');
            OSCOM::redirect(OSCOM::get_link());
        }
        if (Core_Update::local_package_exists() && Core_Update::get_package_info('version_to') != $_GET['v']) {
            Core_Update::delete_package();
        }
        if (!Core_Update::local_package_exists() && !Core_Update::download_package($_GET['v'])) {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_local_update_package_does_not_exist'), 'error');
            OSCOM::redirect(OSCOM::get_link());
        }
        $application->set_page_content('package_contents.php');
        $application->set_page_title(sprintf(OSCOM::get_def('action_heading_apply'), Core_Update::get_package_info('version_from'), Core_Update::get_package_info('version_to')));
    }
}