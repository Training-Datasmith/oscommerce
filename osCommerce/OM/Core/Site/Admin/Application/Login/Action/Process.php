<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Login\Action;

use Os_Commerce\OM\Core\Access;
use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Login\Login;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $data = ['username' => $_POST['user_name'], 'password' => $_POST['user_password']];
        if (Login::is_valid_credentials($data)) {
            Registry::get('Session')->recreate();
            $admin = Login::get_admin($data['username']);
            $_SESSION[OSCOM::get_site()]['id'] = (int) $admin['id'];
            $_SESSION[OSCOM::get_site()]['username'] = $admin['user_name'];
            $_SESSION[OSCOM::get_site()]['access'] = Access::get_user_levels($admin['id']);
            $to_application = OSCOM::get_default_site_application();
            if (isset($_SESSION[OSCOM::get_site()]['redirect_origin'])) {
                $to_application = $_SESSION[OSCOM::get_site()]['redirect_origin'];
                unset($_SESSION[OSCOM::get_site()]['redirect_origin']);
            }
            OSCOM::redirect(OSCOM::get_link(null, $to_application));
        } else {
            Registry::get('MessageStack')->add('header', OSCOM::get_def('ms_error_login_invalid'), 'error');
        }
    }
}