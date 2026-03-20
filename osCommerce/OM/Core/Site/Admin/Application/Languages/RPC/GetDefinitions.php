<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Languages\RPC;

use Os_Commerce\OM\Core\Site\Admin\Application\Languages\Languages;
use Os_Commerce\OM\Core\Site\RPC\Controller as RPC;
class Get_Definitions
{
    public static function execute()
    {
        if (!isset($_GET['search'])) {
            $_GET['search'] = '';
        }
        if (!empty($_GET['search'])) {
            $result = Languages::find_definitions($_GET['id'], $_GET['group'], $_GET['search']);
        } else {
            $result = Languages::get_definitions($_GET['id'], $_GET['group']);
        }
        $result['rpcStatus'] = RPC::STATUS_SUCCESS;
        echo json_encode($result);
    }
}