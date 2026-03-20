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
class Os_C_orders_Status_admin
{
    public static function get_data($id)
    {
        global $os_c_database, $os_c_language;
        $Qstatus = $os_c_database->query('select * from :table_orders_status where orders_status_id = :orders_status_id and language_id = :language_id');
        $Qstatus->bind_table(':table_orders_status', TABLE_ORDERS_STATUS);
        $Qstatus->bind_int(':orders_status_id', $id);
        $Qstatus->bind_int(':language_id', $os_c_language->get_id());
        $Qstatus->execute();
        $data = $Qstatus->to_array();
        $Qstatus->free_result();
        return $data;
    }
    public static function save($id = null, $data, $default = false)
    {
        global $os_c_database, $os_c_language;
        $error = false;
        $os_c_database->start_transaction();
        if (is_numeric($id)) {
            $orders_status_id = $id;
        } else {
            $Qstatus = $os_c_database->query('select max(orders_status_id) as orders_status_id from :table_orders_status');
            $Qstatus->bind_table(':table_orders_status', TABLE_ORDERS_STATUS);
            $Qstatus->execute();
            $orders_status_id = $Qstatus->value_int('orders_status_id') + 1;
        }
        foreach ($os_c_language->get_all() as $l) {
            if (is_numeric($id)) {
                $Qstatus = $os_c_database->query('update :table_orders_status set orders_status_name = :orders_status_name where orders_status_id = :orders_status_id and language_id = :language_id');
            } else {
                $Qstatus = $os_c_database->query('insert into :table_orders_status (orders_status_id, language_id, orders_status_name) values (:orders_status_id, :language_id, :orders_status_name)');
            }
            $Qstatus->bind_table(':table_orders_status', TABLE_ORDERS_STATUS);
            $Qstatus->bind_int(':orders_status_id', $orders_status_id);
            $Qstatus->bind_value(':orders_status_name', $data['name'][$l['id']]);
            $Qstatus->bind_int(':language_id', $l['id']);
            $Qstatus->set_logging($_SESSION['module'], $orders_status_id);
            $Qstatus->execute();
            if ($os_c_database->is_error()) {
                $error = true;
                break;
            }
        }
        if ($error === false) {
            if ($default === true) {
                $Qupdate = $os_c_database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
                $Qupdate->bind_table(':table_configuration', TABLE_CONFIGURATION);
                $Qupdate->bind_int(':configuration_value', $orders_status_id);
                $Qupdate->bind_value(':configuration_key', 'DEFAULT_ORDERS_STATUS_ID');
                $Qupdate->set_logging($_SESSION['module'], $orders_status_id);
                $Qupdate->execute();
                if ($os_c_database->is_error()) {
                    $error = true;
                }
            }
        }
        if ($error === false) {
            $os_c_database->commit_transaction();
            if ($default === true) {
                Os_C_cache::clear('configuration');
            }
            return true;
        }
        $os_c_database->rollback_transaction();
        return false;
    }
    public static function delete($id)
    {
        global $os_c_database;
        $Qstatus = $os_c_database->query('delete from :table_orders_status where orders_status_id = :orders_status_id');
        $Qstatus->bind_table(':table_orders_status', TABLE_ORDERS_STATUS);
        $Qstatus->bind_int(':orders_status_id', $id);
        $Qstatus->set_logging($_SESSION['module'], $id);
        $Qstatus->execute();
        if (!$os_c_database->is_error()) {
            return true;
        }
        return false;
    }
}