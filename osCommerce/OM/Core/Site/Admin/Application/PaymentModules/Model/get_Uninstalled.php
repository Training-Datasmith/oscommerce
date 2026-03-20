<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Payment_Modules\Model;

use Os_Commerce\OM\Core\Directory_Listing;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Payment_Modules\Payment_Modules;
class Get_Uninstalled
{
    public static function execute()
    {
        $OSCOM_Language = Registry::get('Language');
        $installed_modules = Payment_Modules::get_installed();
        $installed = [];
        foreach ($installed_modules['entries'] as $module) {
            $installed[] = $module['code'];
        }
        $result = ['entries' => []];
        $d_lpm = new Directory_Listing(OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Module/Payment');
        $d_lpm->set_include_directories(false);
        foreach ($d_lpm->get_files() as $file) {
            $module = substr($file['name'], 0, strrpos($file['name'], '.'));
            if (!in_array($module, $installed)) {
                $class = 'osCommerce\OM\Core\Site\Admin\Module\Payment\\' . $module;
                $OSCOM_Language->inject_definitions('modules/payment/' . $module . '.xml');
                $OSCOM_PM = new $class();
                $result['entries'][] = ['code' => $OSCOM_PM->get_code(), 'title' => $OSCOM_PM->get_title(), 'sort_order' => $OSCOM_PM->get_sort_order(), 'status' => $OSCOM_PM->is_enabled()];
            }
        }
        $result['total'] = count($result['entries']);
        return $result;
    }
}