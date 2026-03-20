<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Dashboard\Model;

use Os_Commerce\OM\Core\Directory_Listing;
use Os_Commerce\OM\Core\OSCOM;
class Get_Modules
{
    public static function execute()
    {
        $oscom_directory_listing = new Directory_Listing(OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Module/Dashboard');
        $oscom_directory_listing->set_include_directories(false);
        $result = [];
        foreach ($oscom_directory_listing->get_files() as $file) {
            $module = substr($file['name'], 0, strrpos($file['name'], '.'));
            $module_class = 'osCommerce\OM\Core\Site\Admin\Module\Dashboard\\' . $module;
            $OSCOM_Admin_DB_Module = new $module_class();
            if ($OSCOM_Admin_DB_Module->has_data()) {
                $result[] = ['module' => $module, 'title' => $OSCOM_Admin_DB_Module->get_title(), 'link' => $OSCOM_Admin_DB_Module->has_title_link() ? $OSCOM_Admin_DB_Module->get_title_link() : null, 'data' => $OSCOM_Admin_DB_Module->get_data()];
            }
        }
        return $result;
    }
}