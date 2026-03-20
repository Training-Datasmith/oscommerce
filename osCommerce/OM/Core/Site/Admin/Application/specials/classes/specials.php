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
class Os_C_specials_admin
{
    public static function get_data($id)
    {
        global $os_c_database, $os_c_language;
        $Qspecial = $os_c_database->query('select p.products_id, pd.products_name, p.products_price, s.specials_id, s.specials_new_products_price, s.specials_date_added, s.specials_last_modified, s.expires_date, s.date_status_change, s.status from :table_products p, :table_specials s, :table_products_description pd where s.specials_id = :specials_id and s.products_id = p.products_id and p.products_id = pd.products_id and pd.language_id = :language_id limit 1');
        $Qspecial->bind_table(':table_specials', TABLE_SPECIALS);
        $Qspecial->bind_table(':table_products', TABLE_PRODUCTS);
        $Qspecial->bind_table(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
        $Qspecial->bind_int(':specials_id', $id);
        $Qspecial->bind_int(':language_id', $os_c_language->get_id());
        $Qspecial->execute();
        $data = $Qspecial->to_array();
        $Qspecial->free_result();
        return $data;
    }
    public static function save($id = null, $data)
    {
        global $os_c_database;
        $error = false;
        $Qproduct = $os_c_database->query('select products_price from :table_products where products_id = :products_id limit 1');
        $Qproduct->bind_table(':table_products', TABLE_PRODUCTS);
        $Qproduct->bind_int(':products_id', $data['products_id']);
        $Qproduct->execute();
        $specials_price = $data['specials_price'];
        if (substr($specials_price, -1) == '%') {
            $specials_price = $Qproduct->value_decimal('products_price') - (float) $specials_price / 100 * $Qproduct->value_decimal('products_price');
        }
        if ($specials_price < '0.00' || $specials_price >= $Qproduct->value_decimal('products_price')) {
            $error = true;
            //HPDL        $osC_MessageStack->add($this->_module, ERROR_SPECIALS_PRICE, 'error');
        }
        if ($data['expires_date'] < $data['start_date']) {
            $error = true;
            //HPDL        $osC_MessageStack->add($this->_module, ERROR_SPECIALS_DATE, 'error');
        }
        if ($error === false) {
            if (is_numeric($id)) {
                $Qspecial = $os_c_database->query('update :table_specials set specials_new_products_price = :specials_new_products_price, specials_last_modified = now(), expires_date = :expires_date, start_date = :start_date, status = :status where specials_id = :specials_id');
                $Qspecial->bind_int(':specials_id', $id);
            } else {
                $Qspecial = $os_c_database->query('insert into :table_specials (products_id, specials_new_products_price, specials_date_added, expires_date, start_date, status) values (:products_id, :specials_new_products_price, now(), :expires_date, :start_date, :status)');
                $Qspecial->bind_int(':products_id', $data['products_id']);
            }
            $Qspecial->bind_table(':table_specials', TABLE_SPECIALS);
            $Qspecial->bind_value(':specials_new_products_price', $specials_price);
            $Qspecial->bind_date(':expires_date', $data['expires_date']);
            $Qspecial->bind_date(':start_date', $data['start_date']);
            $Qspecial->bind_int(':status', $data['status']);
            $Qspecial->set_logging($_SESSION['module'], $id);
            $Qspecial->execute();
            if ($os_c_database->is_error()) {
                $error = true;
            }
        }
        if ($error === false) {
            return true;
        }
        return false;
    }
    public static function delete($id)
    {
        global $os_c_database;
        $Qspecial = $os_c_database->query('delete from :table_specials where specials_id = :specials_id');
        $Qspecial->bind_table(':table_specials', TABLE_SPECIALS);
        $Qspecial->bind_int(':specials_id', $id);
        $Qspecial->set_logging($_SESSION['module'], $id);
        $Qspecial->execute();
        if (!$os_c_database->is_error()) {
            return true;
        }
        return false;
    }
}