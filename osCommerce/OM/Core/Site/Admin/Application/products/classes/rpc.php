<?php

declare (strict_types=1);
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
require 'includes/applications/products/classes/products.php';
require 'includes/classes/category_tree.php';
require 'includes/classes/image.php';
require '../includes/classes/currencies.php';
class Os_C_products_admin_rpc
{
    public static function get_all()
    {
        global $_module, $os_c_currencies;
        if (!isset($_GET['cID'])) {
            $_GET['cID'] = '0';
        }
        if (!isset($_GET['search'])) {
            $_GET['search'] = '';
        }
        if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
            $_GET['page'] = 1;
        }
        $os_c_currencies = new Os_C_currencies();
        if (!empty($_GET['search'])) {
            $result = Os_C_products_admin::find($_GET['search'], $_GET['cID'], $_GET['page']);
        } else {
            $result = Os_C_products_admin::get_all($_GET['cID'], $_GET['page']);
        }
        $result['rpcStatus'] = RPC_STATUS_SUCCESS;
        echo json_encode($result);
    }
    public static function get_images()
    {
        global $os_c_database, $_module;
        $os_c_image = new Os_C_image_admin();
        $result = ['entries' => []];
        $Qimages = $os_c_database->query('select id, image, default_flag from :table_products_images where products_id = :products_id order by sort_order');
        $Qimages->bind_table(':table_products_images', TABLE_PRODUCTS_IMAGES);
        $Qimages->bind_int(':products_id', $_GET[$_module]);
        $Qimages->execute();
        while ($Qimages->next()) {
            foreach ($os_c_image->get_groups() as $group) {
                $pass = true;
                if (isset($_GET['filter']) && ($_GET['filter'] == 'originals' && $group['id'] != '1')) {
                    $pass = false;
                } elseif (isset($_GET['filter']) && ($_GET['filter'] == 'others' && $group['id'] == '1')) {
                    $pass = false;
                }
                if ($pass === true) {
                    $result['entries'][] = [$Qimages->value_int('id'), $group['id'], $Qimages->value('image'), $group['code'], osc_href_link($os_c_image->get_address($Qimages->value('image'), $group['code']), null, 'NONSSL', false, false, true), number_format(@filesize(DIR_FS_CATALOG . DIR_WS_IMAGES . 'products/' . $group['code'] . '/' . $Qimages->value('image'))), $Qimages->value_int('default_flag')];
                }
            }
        }
        $result['rpcStatus'] = RPC_STATUS_SUCCESS;
        echo json_encode($result);
    }
    public static function get_local_images()
    {
        $os_c_directory_listing = new Os_C_directory_Listing('../images/products/_upload', true);
        $os_c_directory_listing->set_check_extension('gif');
        $os_c_directory_listing->set_check_extension('jpg');
        $os_c_directory_listing->set_check_extension('png');
        $os_c_directory_listing->set_include_directories(false);
        $result = ['entries' => []];
        foreach ($os_c_directory_listing->get_files() as $file) {
            $result['entries'][] = $file['name'];
        }
        $result['rpcStatus'] = RPC_STATUS_SUCCESS;
        echo json_encode($result);
    }
    public static function assign_local_images()
    {
        global $os_c_database, $_module;
        $os_c_image = new Os_C_image_admin();
        if (is_numeric($_GET[$_module]) && isset($_GET['files'])) {
            $default_flag = 1;
            $Qcheck = $os_c_database->query('select id from :table_products_images where products_id = :products_id and default_flag = :default_flag limit 1');
            $Qcheck->bind_table(':table_products_images', TABLE_PRODUCTS_IMAGES);
            $Qcheck->bind_int(':products_id', $_GET[$_module]);
            $Qcheck->bind_int(':default_flag', 1);
            $Qcheck->execute();
            if ($Qcheck->number_of_rows() === 1) {
                $default_flag = 0;
            }
            foreach ($_GET['files'] as $file) {
                $file = basename($file);
                if (file_exists('../images/products/_upload/' . $file)) {
                    copy('../images/products/_upload/' . $file, '../images/products/originals/' . $file);
                    @unlink('../images/products/_upload/' . $file);
                    if (is_numeric($_GET[$_module])) {
                        $Qimage = $os_c_database->query('insert into :table_products_images (products_id, image, default_flag, sort_order, date_added) values (:products_id, :image, :default_flag, :sort_order, :date_added)');
                        $Qimage->bind_table(':table_products_images', TABLE_PRODUCTS_IMAGES);
                        $Qimage->bind_int(':products_id', $_GET[$_module]);
                        $Qimage->bind_value(':image', $file);
                        $Qimage->bind_int(':default_flag', $default_flag);
                        $Qimage->bind_int(':sort_order', 0);
                        $Qimage->bind_raw(':date_added', 'now()');
                        $Qimage->set_logging($_SESSION['module'], $_GET[$_module]);
                        $Qimage->execute();
                        foreach ($os_c_image->get_groups() as $group) {
                            if ($group['id'] != '1') {
                                $os_c_image->resize($file, $group['id']);
                            }
                        }
                    }
                }
            }
        }
        $result = ['result' => 1, 'rpcStatus' => RPC_STATUS_SUCCESS];
        echo json_encode($result);
    }
    public static function set_default_image()
    {
        $os_c_image = new Os_C_image_admin();
        if (isset($_GET['image'])) {
            $os_c_image->set_as_default($_GET['image']);
        }
        $result = ['result' => 1, 'rpcStatus' => RPC_STATUS_SUCCESS];
        echo json_encode($result);
    }
    public static function delete_product_image()
    {
        $os_c_image = new Os_C_image_admin();
        if (isset($_GET['image'])) {
            $os_c_image->delete($_GET['image']);
        }
        $result = ['result' => 1, 'rpcStatus' => RPC_STATUS_SUCCESS];
        echo json_encode($result);
    }
    public static function reorder_images()
    {
        $os_c_image = new Os_C_image_admin();
        if (isset($_GET['image'])) {
            $os_c_image->reorder_images($_GET['image']);
        }
        $result = ['result' => 1, 'rpcStatus' => RPC_STATUS_SUCCESS];
        echo json_encode($result);
    }
    public static function file_upload()
    {
        global $os_c_database, $_module;
        $os_c_image = new Os_C_image_admin();
        if (is_numeric($_GET[$_module])) {
            $products_image = new upload('products_image');
            $products_image->set_extensions(['gif', 'jpg', 'jpeg', 'png']);
            if ($products_image->exists()) {
                $products_image->set_destination(realpath('../images/products/originals'));
                if ($products_image->parse() && $products_image->save()) {
                    $default_flag = 1;
                    $Qcheck = $os_c_database->query('select id from :table_products_images where products_id = :products_id and default_flag = :default_flag limit 1');
                    $Qcheck->bind_table(':table_products_images', TABLE_PRODUCTS_IMAGES);
                    $Qcheck->bind_int(':products_id', $_GET[$_module]);
                    $Qcheck->bind_int(':default_flag', 1);
                    $Qcheck->execute();
                    if ($Qcheck->number_of_rows() === 1) {
                        $default_flag = 0;
                    }
                    $Qimage = $os_c_database->query('insert into :table_products_images (products_id, image, default_flag, sort_order, date_added) values (:products_id, :image, :default_flag, :sort_order, :date_added)');
                    $Qimage->bind_table(':table_products_images', TABLE_PRODUCTS_IMAGES);
                    $Qimage->bind_int(':products_id', $_GET[$_module]);
                    $Qimage->bind_value(':image', $products_image->filename);
                    $Qimage->bind_int(':default_flag', $default_flag);
                    $Qimage->bind_int(':sort_order', 0);
                    $Qimage->bind_raw(':date_added', 'now()');
                    $Qimage->set_logging($_SESSION['module'], $_GET[$_module]);
                    $Qimage->execute();
                    foreach ($os_c_image->get_groups() as $group) {
                        if ($group['id'] != '1') {
                            $os_c_image->resize($products_image->filename, $group['id']);
                        }
                    }
                }
            }
        }
        $result = ['result' => 1, 'rpcStatus' => RPC_STATUS_SUCCESS];
        echo json_encode($result);
    }
}