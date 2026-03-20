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
/**
 * @since v3.0.2
 */
class View_Log
{
    public static function execute(Application_Abstract $application)
    {
        if (!isset($_GET['log']) || empty($_GET['log'])) {
            OSCOM::redirect(OSCOM::get_link());
        }
        if (!Core_Update::log_exists($_GET['log'])) {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_log_file_does_not_exist'), 'error');
            OSCOM::redirect(OSCOM::get_link());
        }
        $application->set_page_content('view_log.php');
    }
}