<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Dashboard\RPC;

use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Dashboard\Dashboard;
class Get_Shortcut_Notifications
{
    public static function execute()
    {
        $site = OSCOM::get_site();
        $result = [];
        if (isset($_SESSION[$site]['id'])) {
            if (isset($_GET['reset']) && !empty($_GET['reset']) && OSCOM::site_application_exists($_GET['reset'])) {
                Dashboard::update_app_date_opened($_SESSION[$site]['id'], $_GET['reset']);
            }
            $shortcuts = [];
            foreach (Dashboard::get_shortcuts($_SESSION[$site]['id']) as $app) {
                $shortcuts[$app['module']] = $app['last_viewed'];
            }
            foreach ($_SESSION[$site]['access'] as $module => $data) {
                if ($data['shortcut'] === true) {
                    if (method_exists('osCommerce\OM\Core\Site\Admin\Application\\' . $data['module'] . '\\' . $data['module'], 'getShortcutNotification') || class_exists('osCommerce\OM\Core\Site\Admin\Application\\' . $data['module'] . '\Model\getShortcutNotification')) {
                        $result[$data['module']] = call_user_func(['osCommerce\OM\Core\Site\Admin\Application\\' . $data['module'] . '\\' . $data['module'], 'getShortcutNotification'], $shortcuts[$data['module']]);
                    }
                }
            }
        }
        echo json_encode($result);
    }
}