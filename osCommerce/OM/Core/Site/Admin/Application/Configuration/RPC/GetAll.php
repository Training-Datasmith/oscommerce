<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Configuration\RPC;

use Os_Commerce\OM\Core\Site\Admin\Application\Configuration\Configuration;
use Os_Commerce\OM\Core\Site\RPC\Controller as RPC;
class Get_All
{
    public static function execute()
    {
        if (!isset($_GET['search'])) {
            $_GET['search'] = '';
        }
        if (!empty($_GET['search'])) {
            $result = Configuration::find($_GET['search']);
        } else {
            $result = Configuration::get_all();
        }
        $result['rpcStatus'] = RPC::STATUS_SUCCESS;
        echo json_encode($result);
    }
}