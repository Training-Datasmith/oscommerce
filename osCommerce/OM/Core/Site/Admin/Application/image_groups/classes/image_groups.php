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
class Os_C_image_Groups_admin
{
    public static function get_data($id)
    {
        global $os_c_database, $os_c_language;
        $Qgroup = $os_c_database->query('select * from :table_products_images_groups where id = :id and language_id = :language_id');
        $Qgroup->bind_table(':table_products_images_groups', TABLE_PRODUCTS_IMAGES_GROUPS);
        $Qgroup->bind_int(':id', $id);
        $Qgroup->bind_int(':language_id', $os_c_language->get_id());
        $Qgroup->execute();
        $data = $Qgroup->to_array();
        $Qgroup->free_result();
        return $data;
    }
    public static function save($id = null, $data, $default = false)
    {
        global $os_c_database, $os_c_language;
        if (is_numeric($id)) {
            $group_id = $id;
        } else {
            $Qgroup = $os_c_database->query('select max(id) as id from :table_products_images_groups');
            $Qgroup->bind_table(':table_products_images_groups', TABLE_PRODUCTS_IMAGES_GROUPS);
            $Qgroup->execute();
            $group_id = $Qgroup->value_int('id') + 1;
        }
        $error = false;
        $os_c_database->start_transaction();
        foreach ($os_c_language->get_all() as $l) {
            if (is_numeric($id)) {
                $Qgroup = $os_c_database->query('update :table_products_images_groups set title = :title, code = :code, size_width = :size_width, size_height = :size_height, force_size = :force_size where id = :id and language_id = :language_id');
            } else {
                $Qgroup = $os_c_database->query('insert into :table_products_images_groups (id, language_id, title, code, size_width, size_height, force_size) values (:id, :language_id, :title, :code, :size_width, :size_height, :force_size)');
            }
            $Qgroup->bind_table(':table_products_images_groups', TABLE_PRODUCTS_IMAGES_GROUPS);
            $Qgroup->bind_int(':id', $group_id);
            $Qgroup->bind_value(':title', $data['title'][$l['id']]);
            $Qgroup->bind_value(':code', $data['code']);
            $Qgroup->bind_int(':size_width', $data['width']);
            $Qgroup->bind_int(':size_height', $data['height']);
            $Qgroup->bind_int(':force_size', $data['force_size'] === true ? 1 : 0);
            $Qgroup->bind_int(':language_id', $l['id']);
            $Qgroup->set_logging($_SESSION['module'], $group_id);
            $Qgroup->execute();
            if ($os_c_database->is_error()) {
                $error = true;
                break;
            }
        }
        if ($error === false) {
            if ($default === true) {
                $Qupdate = $os_c_database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
                $Qupdate->bind_table(':table_configuration', TABLE_CONFIGURATION);
                $Qupdate->bind_int(':configuration_value', $group_id);
                $Qupdate->bind_value(':configuration_key', 'DEFAULT_IMAGE_GROUP_ID');
                $Qupdate->set_logging($_SESSION['module'], $group_id);
                $Qupdate->execute();
                if ($os_c_database->is_error()) {
                    $error = true;
                }
            }
        }
        if ($error === false) {
            $os_c_database->commit_transaction();
            Os_C_cache::clear('images_groups');
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
        $Qdel = $os_c_database->query('delete from :table_products_images_groups where id = :id');
        $Qdel->bind_table(':table_products_images_groups', TABLE_PRODUCTS_IMAGES_GROUPS);
        $Qdel->bind_int(':id', $id);
        $Qdel->set_logging($_SESSION['module'], $id);
        $Qdel->execute();
        if (!$os_c_database->is_error()) {
            return true;
        }
        return false;
    }
}