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
class Os_C_product_Variants_admin
{
    public static function get_data($id, $language_id = null, $key = null)
    {
        global $os_c_database, $os_c_language;
        if (empty($language_id)) {
            $language_id = $os_c_language->get_id();
        }
        $Qgroup = $os_c_database->query('select * from :table_products_variants_groups where id = :id and languages_id = :languages_id');
        $Qgroup->bind_table(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
        $Qgroup->bind_int(':id', $id);
        $Qgroup->bind_int(':languages_id', $language_id);
        $Qgroup->execute();
        $data = $Qgroup->to_array();
        $Qentries = $os_c_database->query('select count(*) as total_entries from :table_products_variants_values where products_variants_groups_id = :products_variants_groups_id');
        $Qentries->bind_table(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
        $Qentries->bind_int(':products_variants_groups_id', $id);
        $Qentries->execute();
        $data['total_entries'] = $Qentries->value_int('total_entries');
        $Qproducts = $os_c_database->query('select count(*) as total_products from :table_products_variants pv, :table_products_variants_values pvv where pvv.products_variants_groups_id = :products_variants_groups_id and pvv.id = pv.products_variants_values_id');
        $Qproducts->bind_table(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
        $Qproducts->bind_table(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
        $Qproducts->bind_int(':products_variants_groups_id', $id);
        $Qproducts->execute();
        $data['total_products'] = $Qproducts->value_int('total_products');
        if (empty($key)) {
            return $data;
        } else {
            return $data[$key];
        }
    }
    public static function save($id = null, $data)
    {
        global $os_c_database, $os_c_language;
        $error = false;
        if (is_numeric($id)) {
            $group_id = $id;
        } else {
            $Qcheck = $os_c_database->query('select max(id) as id from :table_products_variants_groups');
            $Qcheck->bind_table(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
            $Qcheck->execute();
            $group_id = $Qcheck->value_int('id') + 1;
        }
        $os_c_database->start_transaction();
        foreach ($os_c_language->get_all() as $l) {
            if (is_numeric($id)) {
                $Qgroup = $os_c_database->query('update :table_products_variants_groups set title = :title, sort_order = :sort_order, module = :module where id = :id and languages_id = :languages_id');
            } else {
                $Qgroup = $os_c_database->query('insert into :table_products_variants_groups (id, languages_id, title, sort_order, module) values (:id, :languages_id, :title, :sort_order, :module)');
            }
            $Qgroup->bind_table(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
            $Qgroup->bind_int(':id', $group_id);
            $Qgroup->bind_int(':languages_id', $l['id']);
            $Qgroup->bind_value(':title', $data['name'][$l['id']]);
            $Qgroup->bind_int(':sort_order', $data['sort_order']);
            $Qgroup->bind_value(':module', $data['module']);
            $Qgroup->set_logging($_SESSION['module'], $group_id);
            $Qgroup->execute();
            if ($os_c_database->is_error()) {
                $error = true;
                break;
            }
        }
        if ($error === false) {
            $os_c_database->commit_transaction();
            return true;
        }
        $os_c_database->rollback_transaction();
        return false;
    }
    public static function delete($id)
    {
        global $os_c_database;
        $Qdelete = $os_c_database->query('delete from :table_products_variants_groups where id = :id');
        $Qdelete->bind_table(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
        $Qdelete->bind_int(':id', $id);
        $Qdelete->set_logging($_SESSION['module'], $id);
        $Qdelete->execute();
        return !$os_c_database->is_error();
    }
    public static function get_entry($id, $language_id = null)
    {
        global $os_c_database, $os_c_language;
        if (empty($language_id)) {
            $language_id = $os_c_language->get_id();
        }
        $Qentry = $os_c_database->query('select * from :table_products_variants_values where id = :id and languages_id = :languages_id');
        $Qentry->bind_table(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
        $Qentry->bind_int(':id', $id);
        $Qentry->bind_int(':languages_id', $language_id);
        $Qentry->execute();
        $data = $Qentry->to_array();
        $Qproducts = $os_c_database->query('select count(*) as total_products from :table_products_variants where products_variants_values_id = :products_variants_values_id');
        $Qproducts->bind_table(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
        $Qproducts->bind_int(':products_variants_values_id', $Qentry->value_int('id'));
        $Qproducts->execute();
        $data['total_products'] = $Qproducts->value_int('total_products');
        $Qproducts->free_result();
        $Qentry->free_result();
        return $data;
    }
    public static function save_entry($id = null, $data)
    {
        global $os_c_database, $os_c_language;
        $error = false;
        if (is_numeric($id)) {
            $entry_id = $id;
        } else {
            $Qcheck = $os_c_database->query('select max(id) as id from :table_products_variants_values');
            $Qcheck->bind_table(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
            $Qcheck->execute();
            $entry_id = $Qcheck->value_int('id') + 1;
        }
        $os_c_database->start_transaction();
        foreach ($os_c_language->get_all() as $l) {
            if (is_numeric($id)) {
                $Qentry = $os_c_database->query('update :table_products_variants_values set title = :title, sort_order = :sort_order where id = :id and languages_id = :languages_id');
            } else {
                $Qentry = $os_c_database->query('insert into :table_products_variants_values (id, languages_id, products_variants_groups_id, title, sort_order) values (:id, :languages_id, :products_variants_groups_id, :title, :sort_order)');
                $Qentry->bind_int(':products_variants_groups_id', $data['group_id']);
            }
            $Qentry->bind_table(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
            $Qentry->bind_int(':id', $entry_id);
            $Qentry->bind_int(':languages_id', $l['id']);
            $Qentry->bind_value(':title', $data['name'][$l['id']]);
            $Qentry->bind_int(':sort_order', $data['sort_order']);
            $Qentry->set_logging($_SESSION['module'], $entry_id);
            $Qentry->execute();
            if ($os_c_database->is_error()) {
                $error = true;
                break;
            }
        }
        if ($error === false) {
            $os_c_database->commit_transaction();
            return true;
        }
        $os_c_database->rollback_transaction();
        return false;
    }
    public static function delete_entry($id, $group_id)
    {
        global $os_c_database;
        $Qentry = $os_c_database->query('delete from :table_products_variants_values where id = :id and products_variants_groups_id = :products_variants_groups_id');
        $Qentry->bind_table(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
        $Qentry->bind_int(':id', $id);
        $Qentry->bind_int(':products_variants_groups_id', $group_id);
        $Qentry->set_logging($_SESSION['module'], $id);
        $Qentry->execute();
        if (!$os_c_database->is_error()) {
            return true;
        }
        return false;
    }
}