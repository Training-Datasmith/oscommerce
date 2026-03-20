<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Server_Info\RPC;

use Os_Commerce\OM\Core\Site\Admin\Application\Server_Info\Server_Info;
use Os_Commerce\OM\Core\Site\RPC\Controller as RPC;
class Get_All
{
    public static function execute()
    {
        if (!isset($_GET['search'])) {
            $_GET['search'] = '';
        }
        if (!empty($_GET['search'])) {
            $result = Server_Info::find($_GET['search']);
        } else {
            $result = Server_Info::get_all();
        }
        $result['rpcStatus'] = RPC::STATUS_SUCCESS;
        echo json_encode($result);
    }
}