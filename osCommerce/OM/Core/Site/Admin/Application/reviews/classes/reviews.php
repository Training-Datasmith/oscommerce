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
class Os_C_reviews_admin
{
    public static function get_data($id)
    {
        global $os_c_database;
        $Qreview = $os_c_database->query('select r.*, pd.products_name from :table_reviews r left join :table_products_description pd on (r.products_id = pd.products_id and r.languages_id = pd.language_id) where r.reviews_id = :reviews_id');
        $Qreview->bind_table(':table_reviews', TABLE_REVIEWS);
        $Qreview->bind_table(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
        $Qreview->bind_int(':reviews_id', $id);
        $Qreview->execute();
        $data = $Qreview->to_array();
        $Qaverage = $os_c_database->query('select (avg(reviews_rating) / 5 * 100) as average_rating from :table_reviews where products_id = :products_id');
        $Qaverage->bind_table(':table_reviews', TABLE_REVIEWS);
        $Qaverage->bind_int(':products_id', $Qreview->value_int('products_id'));
        $Qaverage->execute();
        $data['average_rating'] = $Qaverage->value('average_rating');
        $Qaverage->free_result();
        $Qreview->free_result();
        return $data;
    }
    public static function save($id, $data)
    {
        global $os_c_database;
        $Qreview = $os_c_database->query('update :table_reviews set reviews_text = :reviews_text, reviews_rating = :reviews_rating, last_modified = now() where reviews_id = :reviews_id');
        $Qreview->bind_table(':table_reviews', TABLE_REVIEWS);
        $Qreview->bind_value(':reviews_text', $data['review']);
        $Qreview->bind_int(':reviews_rating', $data['rating']);
        $Qreview->bind_int(':reviews_id', $id);
        $Qreview->set_logging($_SESSION['module'], $id);
        $Qreview->execute();
        if (!$os_c_database->is_error()) {
            return true;
        }
        return false;
    }
    public static function delete($id)
    {
        global $os_c_database;
        $Qreview = $os_c_database->query('delete from :table_reviews where reviews_id = :reviews_id');
        $Qreview->bind_table(':table_reviews', TABLE_REVIEWS);
        $Qreview->bind_int(':reviews_id', $id);
        $Qreview->set_logging($_SESSION['module'], $id);
        $Qreview->execute();
        if (!$os_c_database->is_error()) {
            return true;
        }
        return false;
    }
}