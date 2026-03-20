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
require 'includes/applications/backup/classes/backup.php';
class Os_C_application_backup extends Os_C_template_admin
{
    /* Protected variables */
    protected $_module = 'backup';
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
        // check if the backup directory exists
        if (!osc_empty(DIR_FS_BACKUP) && is_dir(DIR_FS_BACKUP)) {
            if (!is_writeable(DIR_FS_BACKUP)) {
                $os_c_message_stack->add('header', sprintf($os_c_language->get('ms_error_backup_directory_not_writable'), DIR_FS_BACKUP), 'error');
            }
        } else {
            $os_c_message_stack->add('header', sprintf($os_c_language->get('ms_error_backup_directory_non_existant'), DIR_FS_BACKUP), 'error');
        }
        if (!empty($_GET['action'])) {
            switch ($_GET['action']) {
                case 'backup':
                    $this->_page_contents = 'backup.php';
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        if (Os_C_backup_admin::backup($_POST['compression'], isset($_POST['download_only']) && $_POST['download_only'] == 'yes' ? true : false)) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
                    }
                    break;
                case 'restoreLocal':
                    $this->_page_contents = 'restore_local.php';
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        if (Os_C_backup_admin::restore()) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
                    }
                    break;
                case 'restore':
                    $this->_page_contents = 'restore.php';
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        if (Os_C_backup_admin::restore($_GET['file'])) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
                    }
                    break;
                case 'download':
                    $filename = basename($_GET['file']);
                    $extension = substr($filename, -3);
                    if ($extension == 'zip' || $extension == '.gz' || $extension == 'sql') {
                        if (file_exists(DIR_FS_BACKUP . $filename)) {
                            if ($fp = fopen(DIR_FS_BACKUP . $filename, 'rb')) {
                                $buffer = fread($fp, filesize(DIR_FS_BACKUP . $filename));
                                fclose($fp);
                                header('Content-type: application/x-octet-stream');
                                header('Content-disposition: attachment; filename=' . $filename);
                                echo $buffer;
                                exit;
                            }
                        }
                    }
                    $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_download_link_not_acceptable'), 'error');
                    break;
                case 'forget':
                    if (Os_C_backup_admin::forget()) {
                        $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                    } else {
                        $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                    }
                    osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
                    break;
                case 'delete':
                    $this->_page_contents = 'delete.php';
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        if (Os_C_backup_admin::delete($_GET['file'])) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
                    }
                    break;
                case 'batchDelete':
                    if (isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch'])) {
                        $this->_page_contents = 'batch_delete.php';
                        if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                            $error = false;
                            foreach ($_POST['batch'] as $id) {
                                if (!Os_C_backup_admin::delete($id)) {
                                    $error = true;
                                    break;
                                }
                            }
                            if ($error === false) {
                                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                            } else {
                                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                            }
                            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
                        }
                    }
                    break;
            }
        }
    }
}