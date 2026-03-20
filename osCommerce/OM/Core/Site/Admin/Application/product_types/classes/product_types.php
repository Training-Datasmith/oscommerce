<?php

declare (strict_types=1);
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
class Os_C_product_Types_admin
{
    public static function get($id, $key = null)
    {
        global $os_c_database;
        $Qtype = $os_c_database->query('select * from :table_product_types where id = :id');
        $Qtype->bind_table(':table_product_types', TABLE_PRODUCT_TYPES);
        $Qtype->bind_int(':id', $id);
        $Qtype->execute();
        $Qassignments = $os_c_database->query('select count(distinct action) as total_assignments from :table_product_types_assignments where types_id = :types_id');
        $Qassignments->bind_table(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
        $Qassignments->bind_int(':types_id', $Qtype->value_int('id'));
        $Qassignments->execute();
        $Qproducts = $os_c_database->query('select count(*) as total_products from :table_products where products_types_id = :products_types_id');
        $Qproducts->bind_table(':table_products', TABLE_PRODUCTS);
        $Qproducts->bind_int(':products_types_id', $Qtype->value_int('id'));
        $Qproducts->execute();
        $data = array_merge($Qtype->to_array(), $Qassignments->to_array(), $Qproducts->to_array());
        if (empty($key)) {
            return $data;
        } else {
            return $data[$key];
        }
    }
    public static function get_all($pageset = 1)
    {
        global $os_c_database;
        if (!is_numeric($pageset) || floor($pageset) != $pageset) {
            $pageset = 1;
        }
        $result = ['entries' => []];
        $Qtypes = $os_c_database->query('select SQL_CALC_FOUND_ROWS * from :table_product_types order by title');
        $Qtypes->bind_table(':table_product_types', TABLE_PRODUCT_TYPES);
        if ($pageset !== -1) {
            $Qtypes->set_batch_limit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
        }
        $Qtypes->execute();
        while ($Qtypes->next()) {
            $Qassignments = $os_c_database->query('select count(distinct action) as total_assignments from :table_product_types_assignments where types_id = :types_id');
            $Qassignments->bind_table(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
            $Qassignments->bind_int(':types_id', $Qtypes->value_int('id'));
            $Qassignments->execute();
            $result['entries'][] = array_merge($Qtypes->to_array(), $Qassignments->to_array());
        }
        $result['total'] = $Qtypes->get_batch_size();
        if ($Qtypes->number_of_rows() > 0) {
            $Qassignments->free_result();
        }
        $Qtypes->free_result();
        return $result;
    }
    public static function save($id = null, $data)
    {
        global $os_c_database;
        if (is_numeric($id)) {
            $Qtype = $os_c_database->query('update :table_product_types set title = :title where id = :id');
            $Qtype->bind_int(':id', $id);
        } else {
            $Qtype = $os_c_database->query('insert into :table_product_types (title) values (:title)');
        }
        $Qtype->bind_table(':table_product_types', TABLE_PRODUCT_TYPES);
        $Qtype->bind_value(':title', $data['title']);
        $Qtype->set_logging($_SESSION['module'], $id);
        $Qtype->execute();
        if (!$os_c_database->is_error()) {
            return true;
        }
        return false;
    }
    public static function delete($id)
    {
        global $os_c_database;
        $Qdelete = $os_c_database->query('delete from :table_product_types where id = :id');
        $Qdelete->bind_table(':table_product_types', TABLE_PRODUCT_TYPES);
        $Qdelete->bind_int(':id', $id);
        $Qdelete->set_logging($_SESSION['module'], $id);
        $Qdelete->execute();
        return !$os_c_database->is_error();
    }
    public static function get_assignments($type_id, $action)
    {
        global $os_c_database;
        if (!class_exists('osC_ProductTypes_Actions_' . $action)) {
            include '../includes/modules/product_types/actions/' . $action . '.php';
        }
        $action_title = call_user_func(['osC_ProductTypes_Actions_' . $action, 'getTitle']);
        $action_modules = [];
        $Qmodules = $os_c_database->query('select module from :table_product_types_assignments where types_id = :types_id and action = :action order by sort_order, module');
        $Qmodules->bind_table(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
        $Qmodules->bind_int(':types_id', $type_id);
        $Qmodules->bind_value(':action', $action);
        $Qmodules->execute();
        while ($Qmodules->next()) {
            if (!class_exists('osC_ProductTypes_Modules_' . $Qmodules->value('module'))) {
                include '../includes/modules/product_types/modules/' . $Qmodules->value('module') . '.php';
            }
            $module_title = call_user_func(['osC_ProductTypes_Modules_' . $Qmodules->value('module'), 'getTitle']);
            $action_modules[] = ['module' => $Qmodules->value('module'), 'module_title' => $module_title];
        }
        $result = ['types_id' => $type_id, 'action' => $action, 'action_title' => $action_title, 'modules' => $action_modules];
        return $result;
    }
    public static function get_all_assignments($type_id)
    {
        global $os_c_database;
        $result = ['entries' => []];
        $Qactions = $os_c_database->query('select distinct action from :table_product_types_assignments where types_id = :types_id order by action');
        $Qactions->bind_table(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
        $Qactions->bind_int(':types_id', $type_id);
        $Qactions->execute();
        while ($Qactions->next()) {
            if (!class_exists('osC_ProductTypes_Actions_' . $Qactions->value('action'))) {
                include '../includes/modules/product_types/actions/' . $Qactions->value('action') . '.php';
            }
            $action_title = call_user_func(['osC_ProductTypes_Actions_' . $Qactions->value('action'), 'getTitle']);
            $action_modules = [];
            $Qmodules = $os_c_database->query('select module from :table_product_types_assignments where types_id = :types_id and action = :action order by sort_order, module');
            $Qmodules->bind_table(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
            $Qmodules->bind_int(':types_id', $type_id);
            $Qmodules->bind_value(':action', $Qactions->value('action'));
            $Qmodules->execute();
            while ($Qmodules->next()) {
                if (!class_exists('osC_ProductTypes_Modules_' . $Qmodules->value('module'))) {
                    include '../includes/modules/product_types/modules/' . $Qmodules->value('module') . '.php';
                }
                $module_title = call_user_func(['osC_ProductTypes_Modules_' . $Qmodules->value('module'), 'getTitle']);
                $action_modules[] = ['module' => $Qmodules->value('module'), 'module_title' => $module_title];
            }
            $result['entries'][] = ['types_id' => $type_id, 'action' => $Qactions->value('action'), 'action_title' => $action_title, 'modules' => $action_modules];
        }
        $result['total'] = $Qactions->number_of_rows();
        $Qactions->free_result();
        return $result;
    }
    public static function save_assignments($type_id, $action, $data)
    {
        global $os_c_database;
        $error = false;
        $os_c_database->start_transaction();
        $Qdel = $os_c_database->query('delete from :table_product_types_assignments where types_id = :types_id and action = :action');
        $Qdel->bind_table(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
        $Qdel->bind_int(':types_id', $type_id);
        $Qdel->bind_value(':action', $action);
        $Qdel->set_logging($_SESSION['module'], $type_id);
        $Qdel->execute();
        $counter = 1;
        foreach ($data['modules'] as $module) {
            $Qinsert = $os_c_database->query('insert into :table_product_types_assignments (types_id, action, module, sort_order) values (:types_id, :action, :module, :sort_order)');
            $Qinsert->bind_table(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
            $Qinsert->bind_int(':types_id', $type_id);
            $Qinsert->bind_value(':action', $action);
            $Qinsert->bind_value(':module', $module);
            $Qinsert->bind_int(':sort_order', $counter);
            $Qinsert->set_logging($_SESSION['module'], $type_id);
            $Qinsert->execute();
            if ($os_c_database->is_error()) {
                $error = true;
                break;
            }
            $counter++;
        }
        if ($error === false) {
            $os_c_database->commit_transaction();
            return true;
        }
        $os_c_database->rollback_transaction();
        return false;
    }
    public static function delete_assignments($type_id, $action)
    {
        global $os_c_database;
        $Qdelete = $os_c_database->query('delete from :table_product_types_assignments where types_id = :types_id and action = :action');
        $Qdelete->bind_table(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
        $Qdelete->bind_int(':types_id', $type_id);
        $Qdelete->bind_value(':action', $action);
        $Qdelete->set_logging($_SESSION['module'], $type_id);
        $Qdelete->execute();
        if (!$os_c_database->is_error()) {
            return true;
        }
        return false;
    }
    public static function get_actions($type_id = null)
    {
        global $os_c_database;
        $filter = [];
        if (!empty($type_id)) {
            $Qactions = $os_c_database->query('select distinct action from :table_product_types_assignments where types_id = :types_id order by action');
            $Qactions->bind_table(':table_product_types_assignments', TABLE_PRODUCT_TYPES_ASSIGNMENTS);
            $Qactions->bind_int(':types_id', $type_id);
            $Qactions->execute();
            while ($Qactions->next()) {
                $filter[] = $Qactions->value('action');
            }
        }
        $os_c_directory_listing = new Os_C_directory_Listing('../includes/modules/product_types/actions');
        $os_c_directory_listing->set_include_directories(false);
        $files = $os_c_directory_listing->get_files();
        $actions_array = [];
        foreach ($os_c_directory_listing->get_files() as $file) {
            $class = substr($file['name'], 0, strrpos($file['name'], '.'));
            if (!in_array($class, $filter)) {
                if (!class_exists('osC_ProductTypes_Actions_' . ucfirst($class))) {
                    include '../includes/modules/product_types/actions/' . $file['name'];
                }
                $module_title = call_user_func(['osC_ProductTypes_Actions_' . ucfirst($class), 'getTitle']);
                $actions_array[] = ['id' => $class, 'title' => $module_title];
            }
        }
        return $actions_array;
    }
    public static function get_modules()
    {
        $os_c_directory_listing = new Os_C_directory_Listing('../includes/modules/product_types/modules');
        $os_c_directory_listing->set_include_directories(false);
        $files = $os_c_directory_listing->get_files();
        $modules_array = [];
        foreach ($os_c_directory_listing->get_files() as $file) {
            $class = substr($file['name'], 0, strrpos($file['name'], '.'));
            if (!class_exists('osC_ProductTypes_Modules_' . ucfirst($class))) {
                include '../includes/modules/product_types/modules/' . $file['name'];
            }
            $module_title = call_user_func(['osC_ProductTypes_Modules_' . ucfirst($class), 'getTitle']);
            $modules_array[] = ['id' => $class, 'title' => $module_title];
        }
        return $modules_array;
    }
}