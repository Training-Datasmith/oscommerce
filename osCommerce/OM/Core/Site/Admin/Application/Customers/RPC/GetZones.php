<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Customers\RPC;

use Os_Commerce\OM\Core\Site\RPC\Controller as RPC;
use Os_Commerce\OM\Core\Site\Shop\Address;
/**
 * @since v3.0.2
 */
class Get_Zones
{
    public static function execute()
    {
        $result = ['zones' => Address::get_zones($_GET['country_id']), 'rpcStatus' => RPC::STATUS_SUCCESS];
        echo json_encode($result);
    }
}