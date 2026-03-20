<?php

declare (strict_types=1);
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
require 'includes/applications/product_types/classes/product_types.php';
class Os_C_product_types_admin_rpc
{
    public static function get_all()
    {
        if (!isset($_GET['search'])) {
            $_GET['search'] = '';
        }
        if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
            $_GET['page'] = 1;
        }
        if (!empty($_GET['search'])) {
            $result = Os_C_product_Types_admin::find($_GET['search'], $_GET['page']);
        } else {
            $result = Os_C_product_Types_admin::get_all($_GET['page']);
        }
        $result['rpcStatus'] = RPC_STATUS_SUCCESS;
        echo json_encode($result);
    }
    public static function get_all_assignments()
    {
        global $_module;
        if (!isset($_GET['search'])) {
            $_GET['search'] = '';
        }
        if (!empty($_GET['search'])) {
            $result = Os_C_product_Types_admin::find_assignments($_GET['search'], $_GET[$_module]);
        } else {
            $result = Os_C_product_Types_admin::get_all_assignments($_GET[$_module]);
        }
        $result['rpcStatus'] = RPC_STATUS_SUCCESS;
        echo json_encode($result);
    }
}