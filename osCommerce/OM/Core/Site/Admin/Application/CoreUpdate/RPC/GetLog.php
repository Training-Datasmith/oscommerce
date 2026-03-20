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
/**
 * @since v3.0.2
 */
class Get_Log
{
    public static function execute()
    {
        if (!isset($_GET['search'])) {
            $_GET['search'] = '';
        }
        if (!empty($_GET['search'])) {
            $result = Core_Update::find_log($_GET['log'], $_GET['search']);
        } else {
            $result = Core_Update::get_log($_GET['log']);
        }
        $result['rpcStatus'] = RPC::STATUS_SUCCESS;
        echo json_encode($result);
    }
}