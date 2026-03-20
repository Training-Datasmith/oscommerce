<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Categories\RPC;

use Os_Commerce\OM\Core\Directory_Listing;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\RPC\Controller as RPC;
/**
 * @since v3.0.2
 */
class Get_Available_Images
{
    public static function execute()
    {
        $result = ['images' => []];
        $OSCOM_DL = new Directory_Listing(OSCOM::get_config('dir_fs_public', 'OSCOM') . 'upload');
        $OSCOM_DL->set_include_directories(false);
        $OSCOM_DL->set_check_extension('gif');
        $OSCOM_DL->set_check_extension('jpg');
        $OSCOM_DL->set_check_extension('png');
        foreach ($OSCOM_DL->get_files() as $f) {
            $result['images'][] = $f['name'];
        }
        $result['rpcStatus'] = RPC::STATUS_SUCCESS;
        echo json_encode($result);
    }
}