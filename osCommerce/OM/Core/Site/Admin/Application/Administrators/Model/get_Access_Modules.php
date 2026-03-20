<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Administrators\Model;

use Os_Commerce\OM\Core\Access;
use Os_Commerce\OM\Core\Directory_Listing;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
class Get_Access_Modules
{
    public static function execute()
    {
        $OSCOM_Language = Registry::get('Language');
        $module_files = [];
        $d_lapps = new Directory_Listing(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::get_site() . '/Application');
        $d_lapps->set_include_files(false);
        foreach ($d_lapps->get_files() as $file) {
            if (!in_array($file['name'], call_user_func(['osCommerce\OM\Core\Site\\' . OSCOM::get_site() . '\Controller', 'getGuestApplications'])) && file_exists($d_lapps->get_directory() . '/' . $file['name'] . '/Controller.php')) {
                $module_files[] = $file['name'];
            }
        }
        $d_lcapps = new Directory_Listing(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::get_site() . '/Application');
        $d_lcapps->set_include_files(false);
        foreach ($d_lcapps->get_files() as $file) {
            if (!in_array($file['name'], $module_files) && !in_array($file['name'], call_user_func(['osCommerce\OM\Core\Site\\' . OSCOM::get_site() . '\Controller', 'getGuestApplications'])) && file_exists($d_lcapps->get_directory() . '/' . $file['name'] . '/Controller.php')) {
                $module_files[] = $file['name'];
            }
        }
        $modules = [];
        foreach ($module_files as $module) {
            $application_class = 'osCommerce\OM\Core\Site\\' . OSCOM::get_site() . '\Application\\' . $module . '\Controller';
            if (class_exists($application_class)) {
                if ($module == OSCOM::get_site_application()) {
                    $OSCOM_Application = Registry::get('Application');
                } else {
                    Registry::get('Language')->load_ini_file($module . '.php');
                    $OSCOM_Application = new $application_class(false);
                }
                $modules[Access::get_group_title($OSCOM_Application->get_group())][] = ['id' => $module, 'text' => $OSCOM_Application->get_title(), 'icon' => $OSCOM_Application->get_icon()];
            }
        }
        ksort($modules);
        return $modules;
    }
}