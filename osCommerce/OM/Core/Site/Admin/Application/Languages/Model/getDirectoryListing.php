<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Languages\Model;

use Os_Commerce\OM\Core\Directory_Listing;
use Os_Commerce\OM\Core\OSCOM;
class Get_Directory_Listing
{
    public static function execute()
    {
        $result = [];
        $oscom_directory_listing = new Directory_Listing(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages');
        $oscom_directory_listing->set_include_directories(false);
        $oscom_directory_listing->set_check_extension('xml');
        foreach ($oscom_directory_listing->get_files() as $file) {
            $result[] = substr($file['name'], 0, strrpos($file['name'], '.'));
        }
        return $result;
    }
}