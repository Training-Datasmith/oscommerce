<?php

declare (strict_types=1);
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
class Os_C_orders_admin
{
    public static function delete($id, $restock = false)
    {
        global $os_c_database;
        $error = false;
        $os_c_database->start_transaction();
        if ($restock === true) {
            $Qproducts = $os_c_database->query('select products_id, products_quantity from :table_orders_products where orders_id = :orders_id');
            $Qproducts->bind_table(':table_orders_products', TABLE_ORDERS_PRODUCTS);
            $Qproducts->bind_int(':orders_id', $id);
            $Qproducts->execute();
            while ($Qproducts->next()) {
                $Qupdate = $os_c_database->query('update :table_products set products_quantity = products_quantity + :products_quantity, products_ordered = products_ordered - :products_ordered where products_id = :products_id');
                $Qupdate->bind_table(':table_products', TABLE_PRODUCTS);
                $Qupdate->bind_int(':products_quantity', $Qproducts->value_int('products_quantity'));
                $Qupdate->bind_int(':products_ordered', $Qproducts->value_int('products_quantity'));
                $Qupdate->bind_int(':products_id', $Qproducts->value_int('products_id'));
                $Qupdate->set_logging($_SESSION['module'], $id);
                $Qupdate->execute();
                if ($os_c_database->is_error() === true) {
                    $error = true;
                    break;
                }
                $Qcheck = $os_c_database->query('select products_quantity from :table_products where products_id = :products_id and products_Status = 0');
                $Qcheck->bind_table(':table_products', TABLE_PRODUCTS);
                $Qcheck->bind_int(':products_id', $Qproducts->value_int('products_id'));
                $Qcheck->execute();
                if ($Qcheck->number_of_rows() === 1 && $Qcheck->value_int('products_quantity') > 0) {
                    $Qstatus = $os_c_database->query('update :table_products set products_status = 1 where products_id = :products_id');
                    $Qstatus->bind_table(':table_products', TABLE_PRODUCTS);
                    $Qstatus->bind_int(':products_id', $Qproducts->value_int('products_id'));
                    $Qstatus->set_logging($_SESSION['module'], $id);
                    $Qstatus->execute();
                    if ($os_c_database->is_error() === true) {
                        $error = true;
                        break;
                    }
                }
            }
        }
        if ($error === false) {
            $Qo = $os_c_database->query('delete from :table_orders where orders_id = :orders_id');
            $Qo->bind_table(':table_orders', TABLE_ORDERS);
            $Qo->bind_int(':orders_id', $id);
            $Qo->set_logging($_SESSION['module'], $id);
            $Qo->execute();
            if ($os_c_database->is_error() === true) {
                $error = true;
            }
        }
        if ($error === false) {
            $os_c_database->commit_transaction();
            return true;
        } else {
            $os_c_database->rollback_transaction();
            return false;
        }
    }
}