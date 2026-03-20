<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Tax_Classes\RPC;

use Os_Commerce\OM\Core\Site\Admin\Application\Tax_Classes\Tax_Classes;
use Os_Commerce\OM\Core\Site\RPC\Controller as RPC;
class Get_All_Entries
{
    public static function execute()
    {
        if (!isset($_GET['search'])) {
            $_GET['search'] = '';
        }
        if (!empty($_GET['search'])) {
            $result = Tax_Classes::find_entries($_GET['search'], $_GET['id']);
        } else {
            $result = Tax_Classes::get_all_entries($_GET['id']);
        }
        $result['rpcStatus'] = RPC::STATUS_SUCCESS;
        echo json_encode($result);
    }
}