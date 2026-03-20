<?php

declare (strict_types=1);
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
abstract class Os_C_product_Attributes_admin
{
    protected $_title;
    abstract public function set_function($value);
    public function __construct()
    {
        global $os_c_language;
        $os_c_language->load_ini_file('modules/product_attributes/' . $this->get_code() . '.php');
        $this->_title = $os_c_language->get('product_attributes_' . $this->get_code() . '_title');
    }
    public function get_id()
    {
        global $os_c_database;
        $Qmodule = $os_c_database->query('select id from :table_templates_boxes where code = :code and modules_group = :modules_group');
        $Qmodule->bind_table(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
        $Qmodule->bind_value(':code', $this->get_code());
        $Qmodule->bind_value(':modules_group', 'product_attributes');
        $Qmodule->execute();
        return $Qmodule->number_of_rows() === 1 ? $Qmodule->value_int('id') : 0;
    }
    public function get_code()
    {
        return substr(get_class($this), 22);
    }
    public function get_title()
    {
        return $this->_title;
    }
    public function is_installed()
    {
        return $this->get_id() > 0;
    }
    public function install()
    {
        global $os_c_database;
        $Qinstall = $os_c_database->query('insert into :table_templates_boxes (title, code, author_name, author_www, modules_group) values (:title, :code, :author_name, :author_www, :modules_group)');
        $Qinstall->bind_table(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
        $Qinstall->bind_value(':title', $this->get_title());
        $Qinstall->bind_value(':code', $this->get_code());
        $Qinstall->bind_value(':author_name', '');
        $Qinstall->bind_value(':author_www', '');
        $Qinstall->bind_value(':modules_group', 'product_attributes');
        $Qinstall->execute();
        return $os_c_database->is_error() === false;
    }
    public function uninstall()
    {
        global $os_c_database;
        $error = false;
        $os_c_database->start_transaction();
        $Qdelete = $os_c_database->query('delete from :table_product_attributes where id = :id');
        $Qdelete->bind_table(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
        $Qdelete->bind_int(':id', $this->get_id());
        $Qdelete->execute();
        if ($os_c_database->is_error()) {
            $error = true;
        }
        if ($error === false) {
            $Quninstall = $os_c_database->query('delete from :table_templates_boxes where code = :code and modules_group = :modules_group');
            $Quninstall->bind_table(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
            $Quninstall->bind_value(':code', $this->get_code());
            $Quninstall->bind_value(':modules_group', 'product_attributes');
            $Quninstall->execute();
            if ($os_c_database->is_error()) {
                $error = true;
            }
        }
        if ($error === false) {
            $os_c_database->commit_transaction();
        } else {
            $os_c_database->rollback_transaction();
        }
        return $error === false;
    }
}