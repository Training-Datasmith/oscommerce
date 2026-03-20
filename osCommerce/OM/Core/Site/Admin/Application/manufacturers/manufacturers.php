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
require 'includes/applications/manufacturers/classes/manufacturers.php';
require 'includes/applications/products/classes/products.php';
require 'includes/classes/image.php';
class Os_C_application_manufacturers extends Os_C_template_admin
{
    /* Protected variables */
    protected $_module = 'manufacturers';
    protected $_page_title;
    protected $_page_contents = 'main.php';
    /* Class constructor */
    public function __construct()
    {
        global $os_c_language, $os_c_message_stack, $os_c_image;
        $this->_page_title = $os_c_language->get('heading_title');
        if (!isset($_GET['action'])) {
            $_GET['action'] = '';
        }
        if (!isset($_GET['page']) || isset($_GET['page']) && !is_numeric($_GET['page'])) {
            $_GET['page'] = 1;
        }
        // check if the manufacturers image directory exists
        if (is_dir(realpath('../images/manufacturers'))) {
            if (!is_writeable(realpath('../images/manufacturers'))) {
                $os_c_message_stack->add('header', sprintf($os_c_language->get('ms_error_image_directory_not_writable'), realpath('../images/manufacturers')), 'error');
            }
        } else {
            $os_c_message_stack->add('header', sprintf($os_c_language->get('ms_error_image_directory_non_existant'), realpath('../images/manufacturers')), 'error');
        }
        $os_c_image = new Os_C_image_admin();
        if (!empty($_GET['action'])) {
            switch ($_GET['action']) {
                case 'save':
                    if (isset($_GET['mID']) && is_numeric($_GET['mID'])) {
                        $this->_page_contents = 'edit.php';
                    } else {
                        $this->_page_contents = 'new.php';
                    }
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        $data = ['name' => $_POST['manufacturers_name'], 'url' => $_POST['manufacturers_url']];
                        if (Os_C_manufacturers_admin::save(isset($_GET['mID']) && is_numeric($_GET['mID']) ? $_GET['mID'] : null, $data)) {
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
                        if (Os_C_manufacturers_admin::delete($_GET['mID'], isset($_POST['delete_image']) && $_POST['delete_image'] == 'on' ? true : false, isset($_POST['delete_products']) && $_POST['delete_products'] == 'on' ? true : false)) {
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
                                if (!Os_C_manufacturers_admin::delete($id, isset($_POST['delete_image']) && $_POST['delete_image'] == 'on' ? true : false, isset($_POST['delete_products']) && $_POST['delete_products'] == 'on' ? true : false)) {
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