<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Services\Model;

use Os_Commerce\OM\Core\Directory_Listing;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Services\Services;
/**
 * @since v3.0.2
 */
class Get_Uninstalled
{
    public static function execute()
    {
        $installed_modules = Services::get_installed();
        $installed = [];
        foreach ($installed_modules['entries'] as $module) {
            $installed[] = $module['code'];
        }
        $result = ['entries' => []];
        $d_lsm = new Directory_Listing(OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Module/Service');
        $d_lsm->set_include_directories(false);
        foreach ($d_lsm->get_files() as $file) {
            $module = substr($file['name'], 0, strrpos($file['name'], '.'));
            if (!in_array($module, $installed)) {
                $class = 'osCommerce\OM\Core\Site\Admin\Module\Service\\' . $module;
                $OSCOM_SM = new $class();
                $result['entries'][] = ['code' => $OSCOM_SM->get_code(), 'title' => $OSCOM_SM->get_title()];
            }
        }
        $result['total'] = count($result['entries']);
        return $result;
    }
}