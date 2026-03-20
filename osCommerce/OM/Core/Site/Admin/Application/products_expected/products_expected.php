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
class Os_C_application_products_expected extends Os_C_template_admin
{
    /* Protected variables */
    protected $_module = 'products_expected';
    protected $_page_title;
    protected $_page_contents = 'main.php';
    /* Class constructor */
    public function __construct()
    {
        global $os_c_database, $os_c_language, $os_c_message_stack;
        $this->_page_title = $os_c_language->get('heading_title');
        if (!isset($_GET['action'])) {
            $_GET['action'] = '';
        }
        if (!isset($_GET['page']) || isset($_GET['page']) && !is_numeric($_GET['page'])) {
            $_GET['page'] = 1;
        }
        $Qcheck = $os_c_database->query('select pa.* from :table_product_attributes pa, :table_templates_boxes tb where tb.code = :code and tb.modules_group = :modules_group and tb.id = pa.id and unix_timestamp(now()) > unix_timestamp(str_to_date(pa.value, "%Y-%m-%d"))');
        $Qcheck->bind_table(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
        $Qcheck->bind_table(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
        $Qcheck->bind_value(':code', 'date_available');
        $Qcheck->bind_value(':modules_group', 'product_attributes');
        $Qcheck->execute();
        if ($Qcheck->number_of_rows()) {
            $Qdelete = $os_c_database->query('delete from :table_product_attributes where id = :id and products_id = :products_id');
            $Qdelete->bind_table(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
            $Qdelete->bind_int(':id', $Qcheck->value_int('id'));
            $Qdelete->bind_int(':products_id', $Qcheck->value_int('products_id'));
            $Qdelete->execute();
        }
        if (!empty($_GET['action'])) {
            switch ($_GET['action']) {
                case 'save':
                    $this->_page_contents = 'edit.php';
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        $data = ['date_available' => $_POST['products_date_available']];
                        if (Os_C_products_admin::set_date_available($_GET['pID'], $data)) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
                    }
                    break;
            }
        }
    }
}