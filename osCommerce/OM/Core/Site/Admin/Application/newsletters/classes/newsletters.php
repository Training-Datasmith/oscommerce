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
class Os_C_newsletters_admin
{
    public static function get_data($id)
    {
        global $os_c_database;
        $Qnewsletter = $os_c_database->query('select * from :table_newsletters where newsletters_id = :newsletters_id');
        $Qnewsletter->bind_table(':table_newsletters', TABLE_NEWSLETTERS);
        $Qnewsletter->bind_int(':newsletters_id', $id);
        $Qnewsletter->execute();
        $data = $Qnewsletter->to_array();
        $Qnewsletter->free_result();
        return $data;
    }
    public static function save($id = null, $data)
    {
        global $os_c_database;
        if (is_numeric($id)) {
            $Qemail = $os_c_database->query('update :table_newsletters set title = :title, content = :content, module = :module where newsletters_id = :newsletters_id');
            $Qemail->bind_int(':newsletters_id', $id);
        } else {
            $Qemail = $os_c_database->query('insert into :table_newsletters (title, content, module, date_added, status) values (:title, :content, :module, now(), 0)');
        }
        $Qemail->bind_table(':table_newsletters', TABLE_NEWSLETTERS);
        $Qemail->bind_value(':title', $data['title']);
        $Qemail->bind_value(':content', $data['content']);
        $Qemail->bind_value(':module', $data['module']);
        $Qemail->set_logging($_SESSION['module'], $id);
        $Qemail->execute();
        if (!$os_c_database->is_error()) {
            return true;
        }
        return false;
    }
    public static function delete($id)
    {
        global $os_c_database;
        $Qdelete = $os_c_database->query('delete from :table_newsletters where newsletters_id = :newsletters_id');
        $Qdelete->bind_table(':table_newsletters', TABLE_NEWSLETTERS);
        $Qdelete->bind_int(':newsletters_id', $id);
        $Qdelete->set_logging($_SESSION['module'], $id);
        $Qdelete->execute();
        return !$os_c_database->is_error();
    }
}