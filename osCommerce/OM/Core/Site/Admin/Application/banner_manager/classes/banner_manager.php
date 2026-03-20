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
class Os_C_banner_Manager_admin
{
    public static function get_data($id)
    {
        global $os_c_database;
        $Qbanner = $os_c_database->query('select * from :table_banners where banners_id = :banners_id');
        $Qbanner->bind_table(':table_banners', TABLE_BANNERS);
        $Qbanner->bind_int(':banners_id', $id);
        $Qbanner->execute();
        $data = $Qbanner->to_array();
        $Qbanner->free_result();
        return $data;
    }
    public static function save($id = null, $data)
    {
        global $os_c_database;
        $error = false;
        if (empty($data['html_text']) && empty($data['image_local']) && !empty($data['image'])) {
            $image = new upload($data['image'], realpath('../images/' . $data['image_target']));
            if (!$image->exists() || !$image->parse() || !$image->save()) {
                $error = true;
            }
        }
        if ($error === false) {
            $image_location = !empty($data['image_local']) ? $data['image_local'] : (isset($image) ? $data['image_target'] . $image->filename : null);
            if (is_numeric($id)) {
                $Qbanner = $os_c_database->query('update :table_banners set banners_title = :banners_title, banners_url = :banners_url, banners_image = :banners_image, banners_group = :banners_group, banners_html_text = :banners_html_text, expires_date = :expires_date, expires_impressions = :expires_impressions, date_scheduled = :date_scheduled, status = :status where banners_id = :banners_id');
                $Qbanner->bind_int(':banners_id', $id);
            } else {
                $Qbanner = $os_c_database->query('insert into :table_banners (banners_title, banners_url, banners_image, banners_group, banners_html_text, expires_date, expires_impressions, date_scheduled, status, date_added) values (:banners_title, :banners_url, :banners_image, :banners_group, :banners_html_text, :expires_date, :expires_impressions, :date_scheduled, :status, now())');
            }
            $Qbanner->bind_table(':table_banners', TABLE_BANNERS);
            $Qbanner->bind_value(':banners_title', $data['title']);
            $Qbanner->bind_value(':banners_url', $data['url']);
            $Qbanner->bind_value(':banners_image', $image_location);
            $Qbanner->bind_value(':banners_group', !empty($data['group_new']) ? $data['group_new'] : $data['group']);
            $Qbanner->bind_value(':banners_html_text', $data['html_text']);
            if (empty($data['date_expires'])) {
                $Qbanner->bind_raw(':expires_date', 'null');
                $Qbanner->bind_int(':expires_impressions', $data['expires_impressions']);
            } else {
                $Qbanner->bind_value(':expires_date', $data['date_expires']);
                $Qbanner->bind_int(':expires_impressions', 0);
            }
            if (empty($data['date_scheduled'])) {
                $Qbanner->bind_raw(':date_scheduled', 'null');
                $Qbanner->bind_int(':status', $data['status'] === true ? 1 : 0);
            } else {
                $Qbanner->bind_value(':date_scheduled', $data['date_scheduled']);
                $Qbanner->bind_int(':status', $data['date_scheduled'] > date('Y-m-d') ? 0 : ($data['status'] === true ? 1 : 0));
            }
            $Qbanner->set_logging($_SESSION['module'], $id);
            $Qbanner->execute();
            if (!$os_c_database->is_error()) {
                return true;
            }
        }
        return false;
    }
    public static function delete($id, $delete_image = false)
    {
        global $os_c_database;
        $error = false;
        if ($delete_image === true) {
            $Qimage = $os_c_database->query('select banners_image from :table_banners where banners_id = :banners_id');
            $Qimage->bind_table(':table_banners', TABLE_BANNERS);
            $Qimage->bind_int(':banners_id', $id);
            $Qimage->execute();
        }
        $Qdelete = $os_c_database->query('delete from :table_banners where banners_id = :banners_id');
        $Qdelete->bind_table(':table_banners', TABLE_BANNERS);
        $Qdelete->bind_int(':banners_id', $id);
        $Qdelete->set_logging($_SESSION['module'], $id);
        $Qdelete->execute();
        if ($os_c_database->is_error()) {
            $error = true;
        }
        if ($error === false) {
            if ($delete_image === true) {
                if (!osc_empty($Qimage->value('banners_image'))) {
                    if (is_file('../images/' . $Qimage->value('banners_image')) && is_writeable('../images/' . $Qimage->value('banners_image'))) {
                        @unlink('../images/' . $Qimage->value('banners_image'));
                    }
                }
            }
            $image_extension = osc_dynamic_image_extension();
            if (!empty($image_extension)) {
                if (is_file('images/graphs/banner_yearly-' . $id . '.' . $image_extension) && is_writeable('images/graphs/banner_yearly-' . $id . '.' . $image_extension)) {
                    @unlink('images/graphs/banner_yearly-' . $id . '.' . $image_extension);
                }
                if (is_file('images/graphs/banner_monthly-' . $id . '.' . $image_extension) && is_writeable('images/graphs/banner_monthly-' . $id . '.' . $image_extension)) {
                    @unlink('images/graphs/banner_monthly-' . $id . '.' . $image_extension);
                }
                if (is_file('images/graphs/banner_daily-' . $id . '.' . $image_extension) && is_writeable('images/graphs/banner_daily-' . $id . '.' . $image_extension)) {
                    unlink('images/graphs/banner_daily-' . $id . '.' . $image_extension);
                }
            }
            return true;
        }
        return false;
    }
}