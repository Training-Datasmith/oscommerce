<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Payment_Modules\RPC;

use Os_Commerce\OM\Core\Site\Admin\Application\Payment_Modules\Payment_Modules;
use Os_Commerce\OM\Core\Site\RPC\Controller as RPC;
class Get_Installed
{
    public static function execute()
    {
        if (!isset($_GET['search'])) {
            $_GET['search'] = '';
        }
        if (!empty($_GET['search'])) {
            $result = Payment_Modules::find_installed($_GET['search']);
        } else {
            $result = Payment_Modules::get_installed();
        }
        $result['rpcStatus'] = RPC::STATUS_SUCCESS;
        echo json_encode($result);
    }
}