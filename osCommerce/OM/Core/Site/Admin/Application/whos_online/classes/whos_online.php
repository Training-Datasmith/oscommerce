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
class Os_C_whos_Online_admin
{
    public static function get_data($id)
    {
        global $os_c_database;
        $Qwho = $os_c_database->query('select * from :table_whos_online where session_id = :session_id');
        $Qwho->bind_table(':table_whos_online', TABLE_WHOS_ONLINE);
        $Qwho->bind_value(':session_id', $id);
        $Qwho->execute();
        $data = $Qwho->to_array();
        $Qwho->free_result();
        return $data;
    }
    public static function delete($id)
    {
        global $os_c_database;
        OSCOM_Registry::get('Session')->delete($id);
        $Qwho = $os_c_database->query('delete from :table_whos_online where session_id = :session_id');
        $Qwho->bind_table(':table_whos_online', TABLE_WHOS_ONLINE);
        $Qwho->bind_value(':session_id', $id);
        $Qwho->execute();
        if (!$os_c_database->is_error()) {
            return true;
        }
        return false;
    }
}