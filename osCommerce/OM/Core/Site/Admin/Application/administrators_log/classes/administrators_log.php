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
class Os_C_administrators_Log_admin
{
    public static function get_data($id)
    {
        global $os_c_database;
        $Qlog = $os_c_database->query('select al.id, al.module, al.module_action, al.module_id, al.action, a.user_name, unix_timestamp(al.datestamp) as datestamp from :table_administrators_log al, :table_administrators a where al.id = :id and al.administrators_id = a.id limit 1');
        $Qlog->bind_table(':table_administrators_log', TABLE_ADMINISTRATORS_LOG);
        $Qlog->bind_table(':table_administrators', TABLE_ADMINISTRATORS);
        $Qlog->bind_int(':id', $id);
        $Qlog->execute();
        $data = $Qlog->to_array();
        $Qlog->free_result();
        return $data;
    }
    public static function insert($module, $module_action, $module_id, $action, $log, $transaction_id)
    {
        global $os_c_database;
        if (is_numeric($transaction_id)) {
            $log_id = $transaction_id;
        } else {
            $Qlog = $os_c_database->query('select max(id) as id from :table_administrators_log');
            $Qlog->bind_table(':table_administrators_log', TABLE_ADMINISTRATORS_LOG);
            $Qlog->execute();
            $log_id = $Qlog->value_int('id') + 1;
            if ($transaction_id === true) {
                $os_c_database->logging_transaction = $log_id;
            }
        }
        foreach ($log as $entry) {
            $Qlog = $os_c_database->query('insert into :table_administrators_log (id, module, module_action, module_id, field_key, old_value, new_value, action, administrators_id, datestamp) values (:id, :module, :module_action, :module_id, :field_key, :old_value, :new_value, :action, :administrators_id, now())');
            $Qlog->bind_table(':table_administrators_log', TABLE_ADMINISTRATORS_LOG);
            $Qlog->bind_int(':id', $log_id);
            $Qlog->bind_value(':module', $module);
            $Qlog->bind_value(':module_action', $module_action);
            $Qlog->bind_int(':module_id', $module_id);
            $Qlog->bind_value(':field_key', $entry['key']);
            $Qlog->bind_value(':old_value', $entry['old']);
            $Qlog->bind_value(':new_value', $entry['new']);
            $Qlog->bind_value(':action', $action);
            $Qlog->bind_int(':administrators_id', $_SESSION[OSCOM::get_site()]['id']);
            $Qlog->execute();
        }
    }
    public static function delete($id)
    {
        global $os_c_database;
        $Qlog = $os_c_database->query('delete from :table_administrators_log where id = :id');
        $Qlog->bind_table(':table_administrators_log', TABLE_ADMINISTRATORS_LOG);
        $Qlog->bind_int(':id', $id);
        $Qlog->execute();
        if (!$os_c_database->is_error()) {
            return true;
        }
        return false;
    }
}