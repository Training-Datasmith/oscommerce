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
require 'includes/applications/specials/classes/specials.php';
require 'includes/classes/tax.php';
require '../includes/classes/currencies.php';
class Os_C_application_specials extends Os_C_template_admin
{
    /* Protected variables */
    protected $_module = 'specials';
    protected $_page_title;
    protected $_page_contents = 'main.php';
    /* Class constructor */
    public function __construct()
    {
        global $os_c_language, $os_c_message_stack, $os_c_tax, $os_c_currencies;
        $this->_page_title = $os_c_language->get('heading_title');
        if (!isset($_GET['action'])) {
            $_GET['action'] = '';
        }
        if (!isset($_GET['page']) || isset($_GET['page']) && !is_numeric($_GET['page'])) {
            $_GET['page'] = 1;
        }
        $os_c_tax = new Os_C_tax_admin();
        $os_c_currencies = new Os_C_currencies();
        if (!empty($_GET['action'])) {
            switch ($_GET['action']) {
                case 'save':
                    if (isset($_GET['sID']) && is_numeric($_GET['sID'])) {
                        $this->_page_contents = 'edit.php';
                    } else {
                        $this->_page_contents = 'new.php';
                    }
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        $data = ['products_id' => $_POST['products_id'], 'specials_price' => $_POST['specials_price'], 'expires_date' => $_POST['specials_expires_date'], 'start_date' => $_POST['specials_start_date'], 'status' => isset($_POST['specials_status']) && $_POST['specials_status'] == '1' ? '1' : '0'];
                        if (Os_C_specials_admin::save(isset($_GET['sID']) && is_numeric($_GET['sID']) ? $_GET['sID'] : null, $data)) {
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
                        if (Os_C_specials_admin::delete($_GET['sID'])) {
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
                                if (!Os_C_specials_admin::delete($id)) {
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
            }
        }
    }
}