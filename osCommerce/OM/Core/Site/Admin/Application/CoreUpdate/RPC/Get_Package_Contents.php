<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Core_Update\RPC;

use Os_Commerce\OM\Core\Site\Admin\Application\Core_Update\Core_Update;
use Os_Commerce\OM\Core\Site\RPC\Controller as RPC;
class Get_Package_Contents
{
    public static function execute()
    {
        if (!isset($_GET['search'])) {
            $_GET['search'] = '';
        }
        if (!empty($_GET['search'])) {
            $result = Core_Update::find_package_contents($_GET['search']);
        } else {
            $result = Core_Update::get_package_contents();
        }
        $result['rpcStatus'] = RPC::STATUS_SUCCESS;
        echo json_encode($result);
    }
}