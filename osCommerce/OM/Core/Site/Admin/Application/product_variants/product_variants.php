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
/*
      case 'add_product_attributes':
        $products_id = tep_db_prepare_input($HTTP_POST_VARS['products_id']);
        $options_id = tep_db_prepare_input($HTTP_POST_VARS['options_id']);
        $values_id = tep_db_prepare_input($HTTP_POST_VARS['values_id']);
        $value_price = tep_db_prepare_input($HTTP_POST_VARS['value_price']);
        $price_prefix = tep_db_prepare_input($HTTP_POST_VARS['price_prefix']);

        tep_db_query("insert into " . TABLE_PRODUCTS_ATTRIBUTES . " values ('', '" . (int)$products_id . "', '" . (int)$options_id . "', '" . (int)$values_id . "', '" . tep_db_input($value_price) . "', '" . tep_db_input($price_prefix) . "')");

        if (DOWNLOAD_ENABLED == '1') {
          $products_attributes_id = tep_db_insert_id();

          $products_attributes_filename = tep_db_prepare_input($HTTP_POST_VARS['products_attributes_filename']);
          $products_attributes_maxdays = tep_db_prepare_input($HTTP_POST_VARS['products_attributes_maxdays']);
          $products_attributes_maxcount = tep_db_prepare_input($HTTP_POST_VARS['products_attributes_maxcount']);

          if (!empty($products_attributes_filename)) {
            tep_db_query("insert into " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " values (" . (int)$products_attributes_id . ", '" . tep_db_input($products_attributes_filename) . "', '" . tep_db_input($products_attributes_maxdays) . "', '" . tep_db_input($products_attributes_maxcount) . "')");
          }
        }

        osc_redirect_admin(osc_href_link_admin(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
        break;
*/
require 'includes/applications/product_variants/classes/product_variants.php';
class Os_C_application_product_variants extends Os_C_template_admin
{
    /* Protected variables */
    protected $_module = 'product_variants';
    protected $_page_title;
    protected $_page_contents = 'main.php';
    /* Class constructor */
    public function __construct()
    {
        global $os_c_language, $os_c_message_stack;
        $this->_page_title = $os_c_language->get('heading_title');
        if (!isset($_GET['action'])) {
            $_GET['action'] = '';
        }
        if (!isset($_GET['page']) || isset($_GET['page']) && !is_numeric($_GET['page'])) {
            $_GET['page'] = 1;
        }
        if (!empty($_GET[$this->_module]) && is_numeric($_GET[$this->_module])) {
            $this->_page_contents = 'entries.php';
            $this->_page_title .= ': ' . Os_C_product_Variants_admin::get_data($_GET[$this->_module], null, 'title');
        }
        if (!empty($_GET['action'])) {
            switch ($_GET['action']) {
                case 'save':
                    if (isset($_GET['paID']) && is_numeric($_GET['paID'])) {
                        $this->_page_contents = 'edit.php';
                    } else {
                        $this->_page_contents = 'new.php';
                    }
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        $data = ['name' => $_POST['group_name'], 'module' => $_POST['module'], 'sort_order' => $_POST['sort_order']];
                        if (Os_C_product_Variants_admin::save(isset($_GET['paID']) && is_numeric($_GET['paID']) ? $_GET['paID'] : null, $data)) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
                    }
                    break;
                case 'delete':
                    $this->_page_contents = 'delete.php';
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        if (Os_C_product_Variants_admin::delete($_GET['paID'])) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
                    }
                    break;
                case 'batchDelete':
                    if (isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch'])) {
                        $this->_page_contents = 'batch_delete.php';
                        if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                            $error = false;
                            foreach ($_POST['batch'] as $id) {
                                if (!Os_C_product_Variants_admin::delete($id)) {
                                    $error = true;
                                    break;
                                }
                            }
                            if ($error === false) {
                                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                            } else {
                                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                            }
                            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
                        }
                    }
                    break;
                case 'saveEntry':
                    if (isset($_GET['paeID']) && is_numeric($_GET['paeID'])) {
                        $this->_page_contents = 'entries_edit.php';
                    } else {
                        $this->_page_contents = 'entries_new.php';
                    }
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        $data = ['group_id' => $_GET[$this->_module], 'name' => $_POST['entry_name'], 'sort_order' => $_POST['sort_order']];
                        if (Os_C_product_Variants_admin::save_entry(isset($_GET['paeID']) && is_numeric($_GET['paeID']) ? $_GET['paeID'] : null, $data)) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module] . '&page=' . $_GET['page']));
                    }
                    break;
                case 'deleteEntry':
                    $this->_page_contents = 'entries_delete.php';
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        if (Os_C_product_Variants_admin::delete_entry($_GET['paeID'], $_GET[$this->_module])) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module] . '&page=' . $_GET['page']));
                    }
                    break;
                case 'batchDeleteEntries':
                    if (isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch'])) {
                        $this->_page_contents = 'entries_batch_delete.php';
                        if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                            $error = false;
                            foreach ($_POST['batch'] as $id) {
                                if (!Os_C_product_Variants_admin::delete_entry($id, $_GET[$this->_module])) {
                                    $error = true;
                                    break;
                                }
                            }
                            if ($error === false) {
                                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                            } else {
                                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                            }
                            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module] . '&page=' . $_GET['page']));
                        }
                    }
                    break;
            }
        }
    }
}