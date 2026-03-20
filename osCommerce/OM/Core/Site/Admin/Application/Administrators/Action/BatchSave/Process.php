<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Administrators\Action\Batch_Save;

use Os_Commerce\OM\Core\Access;
use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Administrators\Administrators;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $error = false;
        foreach ($_POST['batch'] as $id) {
            if (!Administrators::set_access_levels($id, $_POST['modules'], $_POST['mode'])) {
                $error = true;
                break;
            }
        }
        if ($error === false) {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_success_action_performed'), 'success');
            if (in_array($_SESSION[OSCOM::get_site()]['id'], $_POST['batch'])) {
                $_SESSION[OSCOM::get_site()]['access'] = Access::get_user_levels($_SESSION[OSCOM::get_site()]['id']);
            }
        } else {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_action_not_performed'), 'error');
        }
        OSCOM::redirect(OSCOM::get_link());
    }
}