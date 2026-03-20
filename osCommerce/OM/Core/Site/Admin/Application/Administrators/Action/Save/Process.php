<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Administrators\Action\Save;

use Os_Commerce\OM\Core\Access;
use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Administrators\Administrators;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $data = ['id' => isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null, 'username' => $_POST['user_name'], 'password' => $_POST['user_password'], 'modules' => isset($_POST['modules']) ? $_POST['modules'] : null];
        switch (Administrators::save($data)) {
            case 1:
                if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] == $_SESSION[OSCOM::get_site()]['id']) {
                    $_SESSION[OSCOM::get_site()]['access'] = Access::get_user_levels($_GET['id']);
                }
                Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_success_action_performed'), 'success');
                OSCOM::redirect(OSCOM::get_link());
                break;
            case -1:
                Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_action_not_performed'), 'error');
                OSCOM::redirect(OSCOM::get_link());
                break;
            case -2:
                Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_username_already_exists'), 'error');
                break;
        }
    }
}