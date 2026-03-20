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
class Os_C_manufacturers_admin
{
    public static function get_data($id, $language_id = null)
    {
        global $os_c_database, $os_c_language;
        if (empty($language_id)) {
            $language_id = $os_c_language->get_id();
        }
        $Qmanufacturers = $os_c_database->query('select m.*, mi.* from :table_manufacturers m, :table_manufacturers_info mi where m.manufacturers_id = :manufacturers_id and m.manufacturers_id = mi.manufacturers_id and mi.languages_id = :languages_id');
        $Qmanufacturers->bind_table(':table_manufacturers', TABLE_MANUFACTURERS);
        $Qmanufacturers->bind_table(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
        $Qmanufacturers->bind_int(':manufacturers_id', $id);
        $Qmanufacturers->bind_int(':languages_id', $language_id);
        $Qmanufacturers->execute();
        $data = $Qmanufacturers->to_array();
        $Qclicks = $os_c_database->query('select sum(url_clicked) as total from :table_manufacturers_info where manufacturers_id = :manufacturers_id');
        $Qclicks->bind_table(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
        $Qclicks->bind_int(':manufacturers_id', $id);
        $Qclicks->execute();
        $data['url_clicks'] = $Qclicks->value_int('total');
        $Qproducts = $os_c_database->query('select count(*) as products_count from :table_products where manufacturers_id = :manufacturers_id');
        $Qproducts->bind_table(':table_products', TABLE_PRODUCTS);
        $Qproducts->bind_int(':manufacturers_id', $id);
        $Qproducts->execute();
        $data['products_count'] = $Qproducts->value_int('products_count');
        $Qclicks->free_result();
        $Qproducts->free_result();
        $Qmanufacturers->free_result();
        return $data;
    }
    public static function save($id = null, $data)
    {
        global $os_c_database, $os_c_language;
        $error = false;
        $os_c_database->start_transaction();
        if (is_numeric($id)) {
            $Qmanufacturer = $os_c_database->query('update :table_manufacturers set manufacturers_name = :manufacturers_name, last_modified = now() where manufacturers_id = :manufacturers_id');
            $Qmanufacturer->bind_int(':manufacturers_id', $id);
        } else {
            $Qmanufacturer = $os_c_database->query('insert into :table_manufacturers (manufacturers_name, date_added) values (:manufacturers_name, now())');
        }
        $Qmanufacturer->bind_table(':table_manufacturers', TABLE_MANUFACTURERS);
        $Qmanufacturer->bind_value(':manufacturers_name', $data['name']);
        $Qmanufacturer->set_logging($_SESSION['module'], $id);
        $Qmanufacturer->execute();
        if (!$os_c_database->is_error()) {
            if (is_numeric($id)) {
                $manufacturers_id = $id;
            } else {
                $manufacturers_id = $os_c_database->next_id();
            }
            $image = new upload('manufacturers_image', realpath('../' . DIR_WS_IMAGES . 'manufacturers'));
            if ($image->exists()) {
                if ($image->parse() && $image->save()) {
                    $Qimage = $os_c_database->query('update :table_manufacturers set manufacturers_image = :manufacturers_image where manufacturers_id = :manufacturers_id');
                    $Qimage->bind_table(':table_manufacturers', TABLE_MANUFACTURERS);
                    $Qimage->bind_value(':manufacturers_image', $image->filename);
                    $Qimage->bind_int(':manufacturers_id', $manufacturers_id);
                    $Qimage->set_logging($_SESSION['module'], $manufacturers_id);
                    $Qimage->execute();
                    if ($os_c_database->is_error()) {
                        $error = true;
                    }
                }
            }
        } else {
            $error = true;
        }
        if ($error === false) {
            foreach ($os_c_language->get_all() as $l) {
                if (is_numeric($id)) {
                    $Qurl = $os_c_database->query('update :table_manufacturers_info set manufacturers_url = :manufacturers_url where manufacturers_id = :manufacturers_id and languages_id = :languages_id');
                } else {
                    $Qurl = $os_c_database->query('insert into :table_manufacturers_info (manufacturers_id, languages_id, manufacturers_url) values (:manufacturers_id, :languages_id, :manufacturers_url)');
                }
                $Qurl->bind_table(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
                $Qurl->bind_int(':manufacturers_id', $manufacturers_id);
                $Qurl->bind_int(':languages_id', $l['id']);
                $Qurl->bind_value(':manufacturers_url', $data['url'][$l['id']]);
                $Qurl->set_logging($_SESSION['module'], $manufacturers_id);
                $Qurl->execute();
                if ($os_c_database->is_error()) {
                    $error = true;
                    break;
                }
            }
        }
        if ($error === false) {
            $os_c_database->commit_transaction();
            Os_C_cache::clear('manufacturers');
            return true;
        }
        $os_c_database->rollback_transaction();
        return false;
    }
    public static function delete($id, $delete_image = false, $delete_products = false)
    {
        global $os_c_database;
        if ($delete_image === true) {
            $Qimage = $os_c_database->query('select manufacturers_image from :table_manufacturers where manufacturers_id = :manufacturers_id');
            $Qimage->bind_table(':table_manufacturers', TABLE_MANUFACTURERS);
            $Qimage->bind_int(':manufacturers_id', $id);
            $Qimage->execute();
            if ($Qimage->number_of_rows() && !osc_empty($Qimage->value('manufacturers_image'))) {
                if (file_exists(realpath('../' . DIR_WS_IMAGES . 'manufacturers/' . $Qimage->value('manufacturers_image')))) {
                    @unlink(realpath('../' . DIR_WS_IMAGES . 'manufacturers/' . $Qimage->value('manufacturers_image')));
                }
            }
        }
        if ($delete_products === true) {
            $Qproducts = $os_c_database->query('select products_id from :table_products where manufacturers_id = :manufacturers_id');
            $Qproducts->bind_table(':table_products', TABLE_PRODUCTS);
            $Qproducts->bind_int(':manufacturers_id', $id);
            $Qproducts->execute();
            while ($Qproducts->next()) {
                Os_C_products_admin::delete($Qproducts->value_int('products_id'));
            }
        }
        $Qm = $os_c_database->query('delete from :table_manufacturers where manufacturers_id = :manufacturers_id');
        $Qm->bind_table(':table_manufacturers', TABLE_MANUFACTURERS);
        $Qm->bind_int(':manufacturers_id', $id);
        $Qm->set_logging($_SESSION['module'], $id);
        $Qm->execute();
        if (!$os_c_database->is_error()) {
            Os_C_cache::clear('manufacturers');
            return true;
        }
        return false;
    }
}