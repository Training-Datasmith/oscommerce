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
require 'includes/applications/products/classes/products.php';
require 'includes/applications/product_attributes/classes/product_attributes.php';
require '../includes/classes/variants.php';
class Os_C_application_products extends Os_C_template_admin
{
    /* Protected variables */
    protected $_module = 'products';
    protected $_page_title;
    protected $_page_contents = 'main.php';
    /* Class constructor */
    public function __construct()
    {
        global $os_c_language, $os_c_message_stack, $os_c_currencies, $os_c_tax, $os_c_category_tree, $os_c_image, $current_category_id;
        $this->_page_title = $os_c_language->get('heading_title');
        $current_category_id = 0;
        if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
            $current_category_id = $_GET['cID'];
        } else {
            $_GET['cID'] = $current_category_id;
        }
        require '../includes/classes/currencies.php';
        $os_c_currencies = new Os_C_currencies();
        require 'includes/classes/tax.php';
        $os_c_tax = new Os_C_tax_admin();
        require 'includes/classes/category_tree.php';
        $os_c_category_tree = new Os_C_category_Tree_admin();
        $os_c_category_tree->set_spacer_string('&nbsp;', 2);
        require 'includes/classes/image.php';
        $os_c_image = new Os_C_image_admin();
        // check if the catalog image directory exists
        if (is_dir(realpath('../images/products'))) {
            if (!is_writeable(realpath('../images/products'))) {
                $os_c_message_stack->add('header', sprintf($os_c_language->get('ms_error_image_directory_not_writable'), realpath('../images/products')), 'error');
            }
        } else {
            $os_c_message_stack->add('header', sprintf($os_c_language->get('ms_error_image_directory_non_existant'), realpath('../images/products')), 'error');
        }
    }
}