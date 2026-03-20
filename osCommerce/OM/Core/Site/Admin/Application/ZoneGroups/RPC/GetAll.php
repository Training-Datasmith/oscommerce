<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Zone_Groups\RPC;

use Os_Commerce\OM\Core\Site\Admin\Application\Zone_Groups\Zone_Groups;
use Os_Commerce\OM\Core\Site\RPC\Controller as RPC;
class Get_All
{
    public static function execute()
    {
        if (!isset($_GET['search'])) {
            $_GET['search'] = '';
        }
        if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
            $_GET['page'] = 1;
        }
        if (!empty($_GET['search'])) {
            $result = Zone_Groups::find($_GET['search'], $_GET['page']);
        } else {
            $result = Zone_Groups::get_all($_GET['page']);
        }
        $result['rpcStatus'] = RPC::STATUS_SUCCESS;
        echo json_encode($result);
    }
}