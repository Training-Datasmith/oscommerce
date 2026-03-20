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
class Os_C_products_admin
{
    public static function get($id)
    {
        global $os_c_database, $os_c_language;
        $Qproducts = $os_c_database->query('select p.*, pd.* from :table_products p, :table_products_description pd where p.products_id = :products_id and p.products_id = pd.products_id and pd.language_id = :language_id');
        $Qproducts->bind_table(':table_products', TABLE_PRODUCTS);
        $Qproducts->bind_table(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
        $Qproducts->bind_int(':products_id', $id);
        $Qproducts->bind_int(':language_id', $os_c_language->get_id());
        $Qproducts->execute();
        $data = $Qproducts->to_array();
        $variants_array = [];
        if ($data['has_children'] == '1') {
            $Qsubproducts = $os_c_database->query('select * from :table_products where parent_id = :parent_id and products_status = :products_status');
            $Qsubproducts->bind_table(':table_products', TABLE_PRODUCTS);
            $Qsubproducts->bind_int(':parent_id', $data['products_id']);
            $Qsubproducts->bind_int(':products_status', 1);
            $Qsubproducts->execute();
            while ($Qsubproducts->next()) {
                $variants_array[$Qsubproducts->value_int('products_id')]['data'] = ['price' => $Qsubproducts->value('products_price'), 'tax_class_id' => $Qsubproducts->value_int('products_tax_class_id'), 'model' => $Qsubproducts->value('products_model'), 'quantity' => $Qsubproducts->value('products_quantity'), 'weight' => $Qsubproducts->value('products_weight'), 'weight_class_id' => $Qsubproducts->value_int('products_weight_class'), 'availability_shipping' => 1];
                $Qvariants = $os_c_database->query('select pv.default_combo, pvg.id as group_id, pvg.title as group_title, pvg.module, pvv.id as value_id, pvv.title as value_title, pvv.sort_order as value_sort_order from :table_products_variants pv, :table_products_variants_groups pvg, :table_products_variants_values pvv where pv.products_id = :products_id and pv.products_variants_values_id = pvv.id and pvv.languages_id = :languages_id and pvv.products_variants_groups_id = pvg.id and pvg.languages_id = :languages_id order by pvg.sort_order, pvg.title');
                $Qvariants->bind_table(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
                $Qvariants->bind_table(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
                $Qvariants->bind_table(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
                $Qvariants->bind_int(':products_id', $Qsubproducts->value_int('products_id'));
                $Qvariants->bind_int(':languages_id', $os_c_language->get_id());
                $Qvariants->bind_int(':languages_id', $os_c_language->get_id());
                $Qvariants->execute();
                while ($Qvariants->next()) {
                    $variants_array[$Qsubproducts->value_int('products_id')]['values'][$Qvariants->value_int('group_id')][$Qvariants->value_int('value_id')] = ['value_id' => $Qvariants->value_int('value_id'), 'group_title' => $Qvariants->value('group_title'), 'value_title' => $Qvariants->value('value_title'), 'sort_order' => $Qvariants->value('value_sort_order'), 'default' => (bool) $Qvariants->value_int('default_combo'), 'module' => $Qvariants->value('module')];
                }
            }
        }
        $data['variants'] = $variants_array;
        $Qattributes = $os_c_database->query('select id, value from :table_product_attributes where products_id = :products_id and languages_id in (0, :languages_id)');
        $Qattributes->bind_table(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
        $Qattributes->bind_int(':products_id', $id);
        $Qattributes->bind_int(':languages_id', $os_c_language->get_id());
        $Qattributes->execute();
        $attributes_array = [];
        while ($Qattributes->next()) {
            $attributes_array[$Qattributes->value_int('id')] = $Qattributes->value('value');
        }
        $data['attributes'] = $attributes_array;
        return $data;
    }
    public static function get_all($category_id = null, $pageset = 1)
    {
        global $os_c_database, $os_c_language, $os_c_currencies;
        if (!is_numeric($category_id)) {
            $category_id = 0;
        }
        if (!is_numeric($pageset) || floor($pageset) != $pageset) {
            $pageset = 1;
        }
        $result = ['entries' => []];
        if ($category_id > 0) {
            $os_c_category_tree = new Os_C_category_Tree_admin();
            $os_c_category_tree->set_breadcrumb_usage(false);
            $in_categories = [$category_id];
            foreach ($os_c_category_tree->get_array($category_id) as $category) {
                $in_categories[] = $category['id'];
            }
            $Qproducts = $os_c_database->query('select SQL_CALC_FOUND_ROWS distinct p.*, pd.products_name from :table_products p, :table_products_description pd, :table_products_to_categories p2c where p.parent_id is null and p.products_id = pd.products_id and pd.language_id = :language_id and p.products_id = p2c.products_id and p2c.categories_id in (:categories_id)');
            $Qproducts->bind_table(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
            $Qproducts->bind_raw(':categories_id', implode(',', $in_categories));
        } else {
            $Qproducts = $os_c_database->query('select SQL_CALC_FOUND_ROWS p.*, pd.products_name from :table_products p, :table_products_description pd where p.parent_id is null and p.products_id = pd.products_id and pd.language_id = :language_id');
        }
        $Qproducts->append_query('order by pd.products_name');
        $Qproducts->bind_table(':table_products', TABLE_PRODUCTS);
        $Qproducts->bind_table(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
        $Qproducts->bind_int(':language_id', $os_c_language->get_id());
        if ($pageset !== -1) {
            $Qproducts->set_batch_limit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
        }
        $Qproducts->execute();
        while ($Qproducts->next()) {
            $price = $os_c_currencies->format($Qproducts->value('products_price'));
            $products_status = $Qproducts->value_int('products_status') === 1;
            $products_quantity = $Qproducts->value_int('products_quantity');
            if ($Qproducts->value_int('has_children') === 1) {
                $Qvariants = $os_c_database->query('select min(products_price) as min_price, max(products_price) as max_price, sum(products_quantity) as total_quantity, min(products_status) as products_status from :table_products where parent_id = :parent_id');
                $Qvariants->bind_table(':table_products', TABLE_PRODUCTS);
                $Qvariants->bind_int(':parent_id', $Qproducts->value_int('products_id'));
                $Qvariants->execute();
                $products_status = $Qvariants->value_int('products_status') === 1;
                $products_quantity = '(' . $Qvariants->value_int('total_quantity') . ')';
                $price = $os_c_currencies->format($Qvariants->value('min_price'));
                if ($Qvariants->value('min_price') != $Qvariants->value('max_price')) {
                    $price .= ' - ' . $os_c_currencies->format($Qvariants->value('max_price'));
                }
            }
            $extra_data = ['products_price_formatted' => $price, 'products_status' => $products_status, 'products_quantity' => $products_quantity];
            $result['entries'][] = array_merge($Qproducts->to_array(), $extra_data);
        }
        $result['total'] = $Qproducts->get_batch_size();
        $Qproducts->free_result();
        return $result;
    }
    public static function find($search, $category_id = null, $pageset = 1)
    {
        global $os_c_database, $os_c_language, $os_c_currencies;
        if (!is_numeric($category_id)) {
            $category_id = 0;
        }
        if (!is_numeric($pageset) || floor($pageset) != $pageset) {
            $pageset = 1;
        }
        $result = ['entries' => []];
        if ($category_id > 0) {
            $os_c_category_tree = new Os_C_category_Tree_admin();
            $os_c_category_tree->set_breadcrumb_usage(false);
            $in_categories = [$category_id];
            foreach ($os_c_category_tree->get_array($category_id) as $category) {
                $in_categories[] = $category['id'];
            }
            $Qproducts = $os_c_database->query('select SQL_CALC_FOUND_ROWS distinct p.*, pd.products_name from :table_products p, :table_products_description pd, :table_products_to_categories p2c where p.parent_id is null and p.products_id = pd.products_id and pd.language_id = :language_id and p.products_id = p2c.products_id and p2c.categories_id in (:categories_id)');
            $Qproducts->bind_table(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
            $Qproducts->bind_raw(':categories_id', implode(',', $in_categories));
        } else {
            $Qproducts = $os_c_database->query('select SQL_CALC_FOUND_ROWS p.*, pd.products_name from :table_products p, :table_products_description pd where p.parent_id is null and p.products_id = pd.products_id and pd.language_id = :language_id');
        }
        $Qproducts->append_query('and (pd.products_name like :products_name or pd.products_keyword like :products_keyword) order by pd.products_name');
        $Qproducts->bind_table(':table_products', TABLE_PRODUCTS);
        $Qproducts->bind_table(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
        $Qproducts->bind_int(':language_id', $os_c_language->get_id());
        $Qproducts->bind_value(':products_name', '%' . $search . '%');
        $Qproducts->bind_value(':products_keyword', '%' . $search . '%');
        if ($pageset !== -1) {
            $Qproducts->set_batch_limit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
        }
        $Qproducts->execute();
        while ($Qproducts->next()) {
            $price = $os_c_currencies->format($Qproducts->value('products_price'));
            $products_status = $Qproducts->value_int('products_status') === 1;
            $products_quantity = $Qproducts->value_int('products_quantity');
            if ($Qproducts->value_int('has_children') === 1) {
                $Qvariants = $os_c_database->query('select min(products_price) as min_price, max(products_price) as max_price, sum(products_quantity) as total_quantity, min(products_status) as products_status from :table_products where parent_id = :parent_id');
                $Qvariants->bind_table(':table_products', TABLE_PRODUCTS);
                $Qvariants->bind_int(':parent_id', $Qproducts->value_int('products_id'));
                $Qvariants->execute();
                $products_status = $Qvariants->value_int('products_status') === 1;
                $products_quantity = '(' . $Qvariants->value_int('total_quantity') . ')';
                $price = $os_c_currencies->format($Qvariants->value('min_price'));
                if ($Qvariants->value('min_price') != $Qvariants->value('max_price')) {
                    $price .= '&nbsp;-&nbsp;' . $os_c_currencies->format($Qvariants->value('max_price'));
                }
            }
            $extra_data = ['products_price_formatted' => $price, 'products_status' => $products_status, 'products_quantity' => $products_quantity];
            $result['entries'][] = array_merge($Qproducts->to_array(), $extra_data);
        }
        $result['total'] = $Qproducts->get_batch_size();
        $Qproducts->free_result();
        return $result;
    }
    public static function save($id = null, $data)
    {
        global $os_c_database, $os_c_language, $os_c_image;
        $error = false;
        $os_c_database->start_transaction();
        if (is_numeric($id)) {
            $Qproduct = $os_c_database->query('update :table_products set products_quantity = :products_quantity, products_price = :products_price, products_model = :products_model, products_weight = :products_weight, products_weight_class = :products_weight_class, products_status = :products_status, products_tax_class_id = :products_tax_class_id, products_last_modified = now() where products_id = :products_id');
            $Qproduct->bind_int(':products_id', $id);
        } else {
            $Qproduct = $os_c_database->query('insert into :table_products (products_quantity, products_price, products_model, products_weight, products_weight_class, products_status, products_tax_class_id, products_date_added) values (:products_quantity, :products_price, :products_model, :products_weight, :products_weight_class, :products_status, :products_tax_class_id, :products_date_added)');
            $Qproduct->bind_raw(':products_date_added', 'now()');
        }
        $Qproduct->bind_table(':table_products', TABLE_PRODUCTS);
        $Qproduct->bind_int(':products_quantity', $data['quantity']);
        $Qproduct->bind_float(':products_price', $data['price']);
        $Qproduct->bind_value(':products_model', $data['model']);
        $Qproduct->bind_float(':products_weight', $data['weight']);
        $Qproduct->bind_int(':products_weight_class', $data['weight_class']);
        $Qproduct->bind_int(':products_status', $data['status']);
        $Qproduct->bind_int(':products_tax_class_id', $data['tax_class_id']);
        //      $Qproduct->setLogging($_SESSION['module'], $id);
        $Qproduct->execute();
        if ($os_c_database->is_error()) {
            $error = true;
        } else {
            if (is_numeric($id)) {
                $products_id = $id;
            } else {
                $products_id = $os_c_database->next_id();
            }
            $Qcategories = $os_c_database->query('delete from :table_products_to_categories where products_id = :products_id');
            $Qcategories->bind_table(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
            $Qcategories->bind_int(':products_id', $products_id);
            //        $Qcategories->setLogging($_SESSION['module'], $products_id);
            $Qcategories->execute();
            if ($os_c_database->is_error()) {
                $error = true;
            } else if (isset($data['categories']) && !empty($data['categories'])) {
                foreach ($data['categories'] as $category_id) {
                    $Qp2c = $os_c_database->query('insert into :table_products_to_categories (products_id, categories_id) values (:products_id, :categories_id)');
                    $Qp2c->bind_table(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
                    $Qp2c->bind_int(':products_id', $products_id);
                    $Qp2c->bind_int(':categories_id', $category_id);
                    //              $Qp2c->setLogging($_SESSION['module'], $products_id);
                    $Qp2c->execute();
                    if ($os_c_database->is_error()) {
                        $error = true;
                        break;
                    }
                }
            }
        }
        if ($error === false) {
            $images = [];
            $products_image = new upload('products_image');
            $products_image->set_extensions(['gif', 'jpg', 'jpeg', 'png']);
            if ($products_image->exists()) {
                $products_image->set_destination(realpath('../images/products/originals'));
                if ($products_image->parse() && $products_image->save()) {
                    $images[] = $products_image->filename;
                }
            }
            if (isset($data['localimages'])) {
                foreach ($data['localimages'] as $image) {
                    $image = basename($image);
                    if (file_exists('../images/products/_upload/' . $image)) {
                        copy('../images/products/_upload/' . $image, '../images/products/originals/' . $image);
                        @unlink('../images/products/_upload/' . $image);
                        $images[] = $image;
                    }
                }
            }
            $default_flag = 1;
            foreach ($images as $image) {
                $Qimage = $os_c_database->query('insert into :table_products_images (products_id, image, default_flag, sort_order, date_added) values (:products_id, :image, :default_flag, :sort_order, :date_added)');
                $Qimage->bind_table(':table_products_images', TABLE_PRODUCTS_IMAGES);
                $Qimage->bind_int(':products_id', $products_id);
                $Qimage->bind_value(':image', $image);
                $Qimage->bind_int(':default_flag', $default_flag);
                $Qimage->bind_int(':sort_order', 0);
                $Qimage->bind_raw(':date_added', 'now()');
                //          $Qimage->setLogging($_SESSION['module'], $products_id);
                $Qimage->execute();
                if ($os_c_database->is_error()) {
                    $error = true;
                } else {
                    foreach ($os_c_image->get_groups() as $group) {
                        if ($group['id'] != '1') {
                            $os_c_image->resize($image, $group['id']);
                        }
                    }
                }
                $default_flag = 0;
            }
        }
        if ($error === false) {
            foreach ($os_c_language->get_all() as $l) {
                if (is_numeric($id)) {
                    $Qpd = $os_c_database->query('update :table_products_description set products_name = :products_name, products_description = :products_description, products_keyword = :products_keyword, products_tags = :products_tags, products_url = :products_url where products_id = :products_id and language_id = :language_id');
                } else {
                    $Qpd = $os_c_database->query('insert into :table_products_description (products_id, language_id, products_name, products_description, products_keyword, products_tags, products_url) values (:products_id, :language_id, :products_name, :products_description, :products_keyword, :products_tags, :products_url)');
                }
                $Qpd->bind_table(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
                $Qpd->bind_int(':products_id', $products_id);
                $Qpd->bind_int(':language_id', $l['id']);
                $Qpd->bind_value(':products_name', $data['products_name'][$l['id']]);
                $Qpd->bind_value(':products_description', $data['products_description'][$l['id']]);
                $Qpd->bind_value(':products_keyword', $data['products_keyword'][$l['id']]);
                $Qpd->bind_value(':products_tags', $data['products_tags'][$l['id']]);
                $Qpd->bind_value(':products_url', $data['products_url'][$l['id']]);
                //          $Qpd->setLogging($_SESSION['module'], $products_id);
                $Qpd->execute();
                if ($os_c_database->is_error()) {
                    $error = true;
                    break;
                }
            }
        }
        if ($error === false) {
            if (isset($data['attributes']) && !empty($data['attributes'])) {
                foreach ($data['attributes'] as $attributes_id => $value) {
                    if (is_array($value)) {
                    } elseif (!empty($value)) {
                        $Qcheck = $os_c_database->query('select id from :table_product_attributes where products_id = :products_id and id = :id limit 1');
                        $Qcheck->bind_table(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
                        $Qcheck->bind_int(':products_id', $products_id);
                        $Qcheck->bind_int(':id', $attributes_id);
                        $Qcheck->execute();
                        if ($Qcheck->number_of_rows() === 1) {
                            $Qattribute = $os_c_database->query('update :table_product_attributes set value = :value where products_id = :products_id and id = :id');
                        } else {
                            $Qattribute = $os_c_database->query('insert into :table_product_attributes (id, products_id, languages_id, value) values (:id, :products_id, :languages_id, :value)');
                            $Qattribute->bind_int(':languages_id', 0);
                        }
                        $Qattribute->bind_table(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
                        $Qattribute->bind_value(':value', $value);
                        $Qattribute->bind_int(':products_id', $products_id);
                        $Qattribute->bind_int(':id', $attributes_id);
                        $Qattribute->execute();
                        if ($os_c_database->is_error()) {
                            $error = true;
                            break;
                        }
                    }
                }
            }
        }
        if ($error === false) {
            $variants_array = [];
            $default_variant_combo = null;
            if (isset($data['variants_combo']) && !empty($data['variants_combo'])) {
                foreach ($data['variants_combo'] as $key => $combos) {
                    if (isset($data['variants_combo_db'][$key])) {
                        $Qsubproduct = $os_c_database->query('update :table_products set products_quantity = :products_quantity, products_price = :products_price, products_model = :products_model, products_weight = :products_weight, products_weight_class = :products_weight_class, products_status = :products_status, products_tax_class_id = :products_tax_class_id where products_id = :products_id');
                        $Qsubproduct->bind_int(':products_id', $data['variants_combo_db'][$key]);
                    } else {
                        $Qsubproduct = $os_c_database->query('insert into :table_products (parent_id, products_quantity, products_price, products_model, products_weight, products_weight_class, products_status, products_tax_class_id, products_date_added) values (:parent_id, :products_quantity, :products_price, :products_model, :products_weight, :products_weight_class, :products_status, :products_tax_class_id, :products_date_added)');
                        $Qsubproduct->bind_int(':parent_id', $products_id);
                        $Qsubproduct->bind_raw(':products_date_added', 'now()');
                    }
                    $Qsubproduct->bind_table(':table_products', TABLE_PRODUCTS);
                    $Qsubproduct->bind_int(':products_quantity', $data['variants_quantity'][$key]);
                    $Qsubproduct->bind_float(':products_price', $data['variants_price'][$key]);
                    $Qsubproduct->bind_value(':products_model', $data['variants_model'][$key]);
                    $Qsubproduct->bind_float(':products_weight', $data['variants_weight'][$key]);
                    $Qsubproduct->bind_int(':products_weight_class', $data['variants_weight_class'][$key]);
                    $Qsubproduct->bind_int(':products_status', $data['variants_status'][$key]);
                    $Qsubproduct->bind_int(':products_tax_class_id', $data['variants_tax_class_id'][$key]);
                    //            $Qsubproduct->setLogging($_SESSION['module'], $id);
                    $Qsubproduct->execute();
                    if (isset($data['variants_combo_db'][$key])) {
                        $subproduct_id = $data['variants_combo_db'][$key];
                    } else {
                        $subproduct_id = $os_c_database->next_id();
                    }
                    if ($data['variants_default_combo'] == $key) {
                        $default_variant_combo = $subproduct_id;
                    }
                    /*
                                if ( $osC_Database->isError() ) {
                                  $error = true;
                                  break;
                                }
                    */
                    $combos_array = explode(';', $combos);
                    foreach ($combos_array as $combo) {
                        list($vgroup, $vvalue) = explode('_', $combo);
                        $variants_array[$subproduct_id][] = $vvalue;
                        $check_combos_array[] = $vvalue;
                        $Qcheck = $os_c_database->query('select products_id from :table_products_variants where products_id = :products_id and products_variants_values_id = :products_variants_values_id');
                        $Qcheck->bind_table(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
                        $Qcheck->bind_int(':products_id', $subproduct_id);
                        $Qcheck->bind_int(':products_variants_values_id', $vvalue);
                        $Qcheck->execute();
                        if ($Qcheck->number_of_rows() < 1) {
                            $Qvcombo = $os_c_database->query('insert into :table_products_variants (products_id, products_variants_values_id) values (:products_id, :products_variants_values_id)');
                            $Qvcombo->bind_table(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
                            $Qvcombo->bind_int(':products_id', $subproduct_id);
                            $Qvcombo->bind_int(':products_variants_values_id', $vvalue);
                            //                $Qvcombo->setLogging($_SESSION['module'], $products_id);
                            $Qvcombo->execute();
                            if ($os_c_database->is_error()) {
                                $error = true;
                                break 2;
                            }
                        }
                    }
                }
            }
            if ($error === false) {
                if (empty($variants_array)) {
                    $Qcheck = $os_c_database->query('select pv.* from :table_products p, :table_products_variants pv where p.parent_id = :parent_id and p.products_id = pv.products_id');
                    $Qcheck->bind_table(':table_products', TABLE_PRODUCTS);
                    $Qcheck->bind_table(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
                    $Qcheck->bind_int(':parent_id', $products_id);
                    $Qcheck->execute();
                    while ($Qcheck->next()) {
                        $Qdel = $os_c_database->query('delete from :table_products_variants where products_id = :products_id');
                        $Qdel->bind_table(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
                        $Qdel->bind_int(':products_id', $Qcheck->value_int('products_id'));
                        $Qdel->execute();
                        $Qdel = $os_c_database->query('delete from :table_products where products_id = :products_id');
                        $Qdel->bind_table(':table_products', TABLE_PRODUCTS);
                        $Qdel->bind_int(':products_id', $Qcheck->value_int('products_id'));
                        $Qdel->execute();
                    }
                } else {
                    $Qcheck = $os_c_database->query('select pv.* from :table_products p, :table_products_variants pv where p.parent_id = :parent_id and p.products_id = pv.products_id and pv.products_id not in (":products_id")');
                    $Qcheck->bind_table(':table_products', TABLE_PRODUCTS);
                    $Qcheck->bind_table(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
                    $Qcheck->bind_int(':parent_id', $products_id);
                    $Qcheck->bind_raw(':products_id', implode('", "', array_keys($variants_array)));
                    $Qcheck->execute();
                    while ($Qcheck->next()) {
                        $Qdel = $os_c_database->query('delete from :table_products_variants where products_id = :products_id and products_variants_values_id = :products_variants_values_id');
                        $Qdel->bind_table(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
                        $Qdel->bind_int(':products_id', $Qcheck->value_int('products_id'));
                        $Qdel->bind_int(':products_variants_values_id', $Qcheck->value_int('products_variants_values_id'));
                        $Qdel->execute();
                        $Qdel = $os_c_database->query('delete from :table_products where products_id = :products_id');
                        $Qdel->bind_table(':table_products', TABLE_PRODUCTS);
                        $Qdel->bind_int(':products_id', $Qcheck->value_int('products_id'));
                        $Qdel->execute();
                    }
                    foreach ($variants_array as $key => $values) {
                        $Qdel = $os_c_database->query('delete from :table_products_variants where products_id = :products_id and products_variants_values_id not in (":products_variants_values_id")');
                        $Qdel->bind_table(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
                        $Qdel->bind_int(':products_id', $key);
                        $Qdel->bind_raw(':products_variants_values_id', implode('", "', $values));
                        $Qdel->execute();
                    }
                }
            }
            $Qupdate = $os_c_database->query('update :table_products set has_children = :has_children where products_id = :products_id');
            $Qupdate->bind_table(':table_products', TABLE_PRODUCTS);
            $Qupdate->bind_int(':has_children', empty($variants_array) ? 0 : 1);
            $Qupdate->bind_int(':products_id', $products_id);
            $Qupdate->execute();
        }
        if ($error === false) {
            $Qupdate = $os_c_database->query('update :table_products_variants set default_combo = :default_combo where products_id in (":products_id")');
            $Qupdate->bind_table(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
            $Qupdate->bind_int(':default_combo', 0);
            $Qupdate->bind_raw(':products_id', implode('", "', array_keys($variants_array)));
            $Qupdate->execute();
            if (is_numeric($default_variant_combo)) {
                $Qupdate = $os_c_database->query('update :table_products_variants set default_combo = :default_combo where products_id = :products_id');
                $Qupdate->bind_table(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
                $Qupdate->bind_int(':default_combo', 1);
                $Qupdate->bind_int(':products_id', $default_variant_combo);
                $Qupdate->execute();
            }
        }
        if ($error === false) {
            $os_c_database->commit_transaction();
            Os_C_cache::clear('categories');
            Os_C_cache::clear('category_tree');
            Os_C_cache::clear('also_purchased');
            return true;
        }
        $os_c_database->rollback_transaction();
        return false;
    }
    public static function copy($id, $category_id, $type)
    {
        global $os_c_database;
        $category_array = explode('_', $category_id);
        if ($type == 'link') {
            $Qcheck = $os_c_database->query('select count(*) as total from :table_products_to_categories where products_id = :products_id and categories_id = :categories_id');
            $Qcheck->bind_table(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
            $Qcheck->bind_int(':products_id', $id);
            $Qcheck->bind_int(':categories_id', end($category_array));
            $Qcheck->execute();
            if ($Qcheck->value_int('total') < 1) {
                $Qcat = $os_c_database->query('insert into :table_products_to_categories (products_id, categories_id) values (:products_id, :categories_id)');
                $Qcat->bind_table(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
                $Qcat->bind_int(':products_id', $id);
                $Qcat->bind_int(':categories_id', end($category_array));
                $Qcat->set_logging($_SESSION['module'], $id);
                $Qcat->execute();
                if ($Qcat->affected_rows()) {
                    return true;
                }
            }
        } elseif ($type == 'duplicate') {
            $Qproduct = $os_c_database->query('select * from :table_products where products_id = :products_id');
            $Qproduct->bind_table(':table_products', TABLE_PRODUCTS);
            $Qproduct->bind_int(':products_id', $id);
            $Qproduct->execute();
            if ($Qproduct->number_of_rows() === 1) {
                $error = false;
                $os_c_database->start_transaction();
                $Qnew = $os_c_database->query('insert into :table_products (products_quantity, products_price, products_model, products_date_added, products_weight, products_weight_class, products_status, products_tax_class_id, manufacturers_id) values (:products_quantity, :products_price, :products_model, now(), :products_weight, :products_weight_class, 0, :products_tax_class_id, :manufacturers_id)');
                $Qnew->bind_table(':table_products', TABLE_PRODUCTS);
                $Qnew->bind_int(':products_quantity', $Qproduct->value_int('products_quantity'));
                $Qnew->bind_value(':products_price', $Qproduct->value('products_price'));
                $Qnew->bind_value(':products_model', $Qproduct->value('products_model'));
                $Qnew->bind_value(':products_weight', $Qproduct->value('products_weight'));
                $Qnew->bind_int(':products_weight_class', $Qproduct->value_int('products_weight_class'));
                $Qnew->bind_int(':products_tax_class_id', $Qproduct->value_int('products_tax_class_id'));
                $Qnew->bind_int(':manufacturers_id', $Qproduct->value_int('manufacturers_id'));
                $Qnew->set_logging($_SESSION['module']);
                $Qnew->execute();
                if ($Qnew->affected_rows()) {
                    $new_product_id = $os_c_database->next_id();
                    $Qdesc = $os_c_database->query('select * from :table_products_description where products_id = :products_id');
                    $Qdesc->bind_table(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
                    $Qdesc->bind_int(':products_id', $id);
                    $Qdesc->execute();
                    while ($Qdesc->next()) {
                        $Qnewdesc = $os_c_database->query('insert into :table_products_description (products_id, language_id, products_name, products_description, products_tags, products_url, products_viewed) values (:products_id, :language_id, :products_name, :products_description, :products_tags, :products_url, 0)');
                        $Qnewdesc->bind_table(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
                        $Qnewdesc->bind_int(':products_id', $new_product_id);
                        $Qnewdesc->bind_int(':language_id', $Qdesc->value_int('language_id'));
                        $Qnewdesc->bind_value(':products_name', $Qdesc->value('products_name'));
                        $Qnewdesc->bind_value(':products_tags', $Qdesc->value('products_tags'));
                        $Qnewdesc->bind_value(':products_description', $Qdesc->value('products_description'));
                        $Qnewdesc->bind_value(':products_url', $Qdesc->value('products_url'));
                        $Qnewdesc->set_logging($_SESSION['module'], $new_product_id);
                        $Qnewdesc->execute();
                        if ($os_c_database->is_error()) {
                            $error = true;
                            break;
                        }
                    }
                    if ($error === false) {
                        $Qp2c = $os_c_database->query('insert into :table_products_to_categories (products_id, categories_id) values (:products_id, :categories_id)');
                        $Qp2c->bind_table(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
                        $Qp2c->bind_int(':products_id', $new_product_id);
                        $Qp2c->bind_int(':categories_id', end($category_array));
                        $Qp2c->set_logging($_SESSION['module'], $new_product_id);
                        $Qp2c->execute();
                        if ($os_c_database->is_error()) {
                            $error = true;
                        }
                    }
                } else {
                    $error = true;
                }
                if ($error === false) {
                    $os_c_database->commit_transaction();
                    Os_C_cache::clear('categories');
                    Os_C_cache::clear('category_tree');
                    Os_C_cache::clear('also_purchased');
                    return true;
                } else {
                    $os_c_database->rollback_transaction();
                }
            }
        }
        return false;
    }
    public static function delete($id)
    {
        global $os_c_database, $os_c_image;
        $Qim = $os_c_database->query('select id from :table_products_images where products_id = :products_id');
        $Qim->bind_table(':table_products_images', TABLE_PRODUCTS_IMAGES);
        $Qim->bind_int(':products_id', $id);
        $Qim->execute();
        while ($Qim->next()) {
            $os_c_image->delete($Qim->value_int('id'));
        }
        $Qp = $os_c_database->query('delete from :table_products where products_id = :products_id');
        $Qp->bind_table(':table_products', TABLE_PRODUCTS);
        $Qp->bind_int(':products_id', $id);
        $Qp->set_logging($_SESSION['module'], $id);
        $Qp->execute();
        if (!$os_c_database->is_error()) {
            Os_C_cache::clear('categories');
            Os_C_cache::clear('category_tree');
            Os_C_cache::clear('also_purchased');
            Os_C_cache::clear('box-whats_new');
            return true;
        }
        return false;
    }
    public static function set_date_available($id, $data)
    {
        global $os_c_database;
        $Qattribute = $os_c_database->query('select pa.id from :table_product_attributes pa, :table_templates_boxes tb where tb.code = :code and tb.modules_group = :modules_group and tb.id = pa.id and products_id = :products_id');
        $Qattribute->bind_table(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
        $Qattribute->bind_table(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
        $Qattribute->bind_value(':code', 'date_available');
        $Qattribute->bind_value(':modules_group', 'product_attributes');
        $Qattribute->bind_int(':products_id', $id);
        $Qattribute->execute();
        $Qupdate = $os_c_database->query('update :table_product_attributes set value = :value where id = :id and products_id = :products_id');
        $Qupdate->bind_table(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
        $Qupdate->bind_date(':value', $data['date_available']);
        $Qupdate->bind_int(':id', $Qattribute->value_int('id'));
        $Qupdate->bind_int(':products_id', $id);
        $Qupdate->set_logging($_SESSION['module'], $id);
        $Qupdate->execute();
        return $Qupdate->affected_rows() > 0;
    }
    public static function get_keyword_count($keyword, $id = null)
    {
        global $os_c_database;
        $Qkeywords = $os_c_database->query('select count(*) as total from :table_products_description where products_keyword = :products_keyword');
        if (is_numeric($id)) {
            $Qkeywords->append_query('and products_id != :products_id');
            $Qkeywords->bind_int(':products_id', $id);
        }
        $Qkeywords->bind_table(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
        $Qkeywords->bind_value(':products_keyword', $keyword);
        $Qkeywords->execute();
        return $Qkeywords->value_int('total');
    }
}