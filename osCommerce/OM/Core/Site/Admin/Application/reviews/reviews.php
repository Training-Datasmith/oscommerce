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
require 'includes/applications/reviews/classes/reviews.php';
class Os_C_application_reviews extends Os_C_template_admin
{
    /* Protected variables */
    protected $_module = 'reviews';
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
        if (!empty($_GET['action'])) {
            switch ($_GET['action']) {
                case 'preview':
                    $this->_page_contents = 'preview.php';
                    break;
                case 'save':
                    $this->_page_contents = 'edit.php';
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        $data = ['review' => $_POST['reviews_text'], 'rating' => $_POST['reviews_rating']];
                        if (Os_C_reviews_admin::save($_GET['rID'], $data)) {
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
                        if (Os_C_reviews_admin::delete($_GET['rID'])) {
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
                                if (!Os_C_reviews_admin::delete($id)) {
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
                case 'rApprove':
                    $this->_approve();
                    break;
                case 'rReject':
                    $this->_reject();
                    break;
            }
        }
    }
    /* Private methods */
    public function _approve()
    {
        global $os_c_database, $os_c_language, $os_c_message_stack;
        if (isset($_GET['rID']) && is_numeric($_GET['rID'])) {
            $Qreview = $os_c_database->query('update :table_reviews set reviews_status = 1 where reviews_id = :reviews_id');
            $Qreview->bind_table(':table_reviews', TABLE_REVIEWS);
            $Qreview->bind_int(':reviews_id', $_GET['rID']);
            $Qreview->set_logging($_SESSION['module'], $_GET['rID']);
            $Qreview->execute();
            if (!$os_c_database->is_error()) {
                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
            } else {
                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
            }
            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
        }
    }
    public function _reject()
    {
        global $os_c_database, $os_c_language, $os_c_message_stack;
        if (isset($_GET['rID']) && is_numeric($_GET['rID'])) {
            $Qreview = $os_c_database->query('update :table_reviews set reviews_status = 2 where reviews_id = :reviews_id');
            $Qreview->bind_table(':table_reviews', TABLE_REVIEWS);
            $Qreview->bind_int(':reviews_id', $_GET['rID']);
            $Qreview->set_logging($_SESSION['module'], $_GET['rID']);
            $Qreview->execute();
            if (!$os_c_database->is_error()) {
                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
            } else {
                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
            }
            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
        }
    }
}