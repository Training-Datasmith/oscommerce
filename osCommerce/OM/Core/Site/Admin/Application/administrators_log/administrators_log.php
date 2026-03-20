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
class Os_C_application_administrators_log extends Os_C_template_admin
{
    /* Protected variables */
    protected $_module = 'administrators_log';
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
        if (!isset($_GET['fm'])) {
            $_GET['fm'] = '';
        }
        if (!isset($_GET['fu']) || !is_numeric($_GET['fu'])) {
            $_GET['fu'] = '';
        }
        if (!empty($_GET['action'])) {
            switch ($_GET['action']) {
                case 'info':
                    $this->_page_contents = 'info.php';
                    break;
                case 'delete':
                    $this->_page_contents = 'delete.php';
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        if (Os_C_administrators_Log_admin::delete($_GET['lID'])) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu']));
                    }
                    break;
                case 'batchDelete':
                    if (isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch'])) {
                        $this->_page_contents = 'batch_delete.php';
                        if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                            $error = false;
                            foreach ($_POST['batch'] as $id) {
                                if (!Os_C_administrators_Log_admin::delete($id)) {
                                    $error = true;
                                    break;
                                }
                            }
                            if ($error === false) {
                                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                            } else {
                                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                            }
                            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu']));
                        }
                    }
                    break;
            }
        }
    }
}