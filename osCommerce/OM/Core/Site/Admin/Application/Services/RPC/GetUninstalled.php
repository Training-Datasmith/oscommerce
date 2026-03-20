<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Services\RPC;

use Os_Commerce\OM\Core\Site\Admin\Application\Services\Services;
use Os_Commerce\OM\Core\Site\RPC\Controller as RPC;
/**
 * @since v3.0.2
 */
class Get_Uninstalled
{
    public static function execute()
    {
        if (!isset($_GET['search'])) {
            $_GET['search'] = '';
        }
        if (!empty($_GET['search'])) {
            $result = Services::find_uninstalled($_GET['search']);
        } else {
            $result = Services::get_uninstalled();
        }
        $result['rpcStatus'] = RPC::STATUS_SUCCESS;
        echo json_encode($result);
    }
}