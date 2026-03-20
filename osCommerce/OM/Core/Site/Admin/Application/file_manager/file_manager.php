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
require 'includes/applications/file_manager/classes/file_manager.php';
define('OSC_ADMIN_FILE_MANAGER_ROOT_PATH', realpath('../'));
class Os_C_application_file_manager extends Os_C_template_admin
{
    /* Protected variables */
    protected $_module = 'file_manager';
    protected $_page_title;
    protected $_page_contents = 'main.php';
    /* Class constructor */
    public function __construct()
    {
        global $os_c_language, $os_c_message_stack;
        $this->_page_title = $os_c_language->get('heading_title');
        if (!isset($_SESSION['fm_directory'])) {
            $_SESSION['fm_directory'] = OSC_ADMIN_FILE_MANAGER_ROOT_PATH;
        }
        if (isset($_GET['directory'])) {
            $_SESSION['fm_directory'] .= '/' . $_GET['directory'];
        } elseif (isset($_GET['goto'])) {
            $_SESSION['fm_directory'] = OSC_ADMIN_FILE_MANAGER_ROOT_PATH . '/' . urldecode($_GET['goto']);
        }
        $_SESSION['fm_directory'] = realpath($_SESSION['fm_directory']);
        if (substr($_SESSION['fm_directory'], 0, strlen(OSC_ADMIN_FILE_MANAGER_ROOT_PATH)) != OSC_ADMIN_FILE_MANAGER_ROOT_PATH || !is_dir($_SESSION['fm_directory'])) {
            $_SESSION['fm_directory'] = OSC_ADMIN_FILE_MANAGER_ROOT_PATH;
        }
        if (!isset($_GET['action'])) {
            $_GET['action'] = '';
        }
        if (!empty($_GET['action'])) {
            switch ($_GET['action']) {
                case 'saveDirectory':
                    $this->_page_contents = 'directory_new.php';
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        if (Os_C_file_Manager_admin::create_directory($_POST['directory_name'], $_SESSION['fm_directory'])) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
                    }
                    break;
                case 'save':
                    if (isset($_GET['entry']) && !empty($_GET['entry'])) {
                        $this->_page_contents = 'file_edit.php';
                    } else {
                        $this->_page_contents = 'file_new.php';
                    }
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        if (Os_C_file_Manager_admin::save_file($_POST['filename'], $_POST['contents'], $_SESSION['fm_directory'])) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
                    }
                    break;
                case 'upload':
                    $this->_page_contents = 'upload.php';
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        $error = false;
                        for ($i = 0; $i < 10; $i++) {
                            if (is_uploaded_file($_FILES['file_' . $i]['tmp_name'])) {
                                if (!Os_C_file_Manager_admin::store_file_upload('file_' . $i, $_SESSION['fm_directory'])) {
                                    $error = true;
                                    break;
                                }
                            }
                        }
                        if ($error === false) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
                    }
                    break;
                case 'delete':
                    $this->_page_contents = 'delete.php';
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        if (Os_C_file_Manager_admin::delete($_GET['entry'], $_SESSION['fm_directory'])) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
                    }
                    break;
                case 'download':
                    $filename = basename($_GET['entry']);
                    if (file_exists($_SESSION['fm_directory'] . '/' . $filename)) {
                        header('Content-type: application/x-octet-stream');
                        header('Content-disposition: attachment; filename=' . urldecode($filename));
                        readfile($_SESSION['fm_directory'] . '/' . $filename);
                        exit;
                    }
                    $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_download_link_invalid'), 'error');
                    break;
            }
        }
    }
}