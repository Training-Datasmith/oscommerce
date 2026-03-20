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
class Os_C_application_templates_modules_layout extends Os_C_template_admin
{
    /* Protected variables */
    protected $_module = 'templates_modules_layout';
    protected $_page_title;
    protected $_page_contents = 'main.php';
    /* Class constructor */
    public function __construct()
    {
        global $os_c_language, $os_c_message_stack;
        if (!isset($_GET['set'])) {
            $_GET['set'] = '';
        }
        if (!isset($_GET['filter'])) {
            $_GET['filter'] = DEFAULT_TEMPLATE;
        }
        if (!isset($_GET['action'])) {
            $_GET['action'] = '';
        }
        switch ($_GET['set']) {
            case 'content':
                $this->_page_title = $os_c_language->get('heading_title_content');
                break;
            case 'boxes':
            default:
                $_GET['set'] = 'boxes';
                $this->_page_title = $os_c_language->get('heading_title_boxes');
                break;
        }
        if (!empty($_GET['action'])) {
            switch ($_GET['action']) {
                case 'save':
                    if (isset($_GET['lID']) && is_numeric($_GET['lID'])) {
                        $this->_page_contents = 'edit.php';
                    } else {
                        $this->_page_contents = 'new.php';
                    }
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        $data = ['box' => $_POST['box'], 'content_page' => $_POST['content_page'], 'page_specific' => isset($_POST['page_specific']) && $_POST['page_specific'] == 'on' ? true : false, 'group' => isset($_POST['group']) && !empty($_POST['group']) ? $_POST['group'] : $_POST['group_new'], 'sort_order' => $_POST['sort_order']];
                        if ($this->_save(isset($_GET['lID']) && is_numeric($_GET['lID']) ? $_GET['lID'] : null, $data, $_GET['set'])) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter']));
                    }
                    break;
                case 'delete':
                    $this->_page_contents = 'delete.php';
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        if ($this->_delete($_GET['lID'], $_GET['set'])) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter']));
                    }
                    break;
                case 'batchDelete':
                    if (isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch'])) {
                        $this->_page_contents = 'batch_delete.php';
                        if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                            $error = false;
                            foreach ($_POST['batch'] as $id) {
                                if (!$this->_delete($id, $_GET['set'])) {
                                    $error = true;
                                    break;
                                }
                            }
                            if ($error === false) {
                                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                            } else {
                                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                            }
                            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter']));
                        }
                    }
                    break;
            }
        }
    }
    /* Private methods */
    public function _save($id = null, $data, $set)
    {
        global $os_c_database;
        $link = explode('/', $data['content_page'], 2);
        if (is_numeric($id)) {
            $Qlayout = $os_c_database->query('update :table_templates_boxes_to_pages set content_page = :content_page, boxes_group = :boxes_group, sort_order = :sort_order, page_specific = :page_specific where id = :id');
            $Qlayout->bind_int(':id', $id);
        } else {
            $Qlayout = $os_c_database->query('insert into :table_templates_boxes_to_pages (templates_boxes_id, templates_id, content_page, boxes_group, sort_order, page_specific) values (:templates_boxes_id, :templates_id, :content_page, :boxes_group, :sort_order, :page_specific)');
            $Qlayout->bind_int(':templates_boxes_id', $data['box']);
            $Qlayout->bind_int(':templates_id', $link[0]);
        }
        $Qlayout->bind_table(':table_templates_boxes_to_pages', TABLE_TEMPLATES_BOXES_TO_PAGES);
        $Qlayout->bind_value(':content_page', $link[1]);
        $Qlayout->bind_value(':boxes_group', $data['group']);
        $Qlayout->bind_int(':sort_order', $data['sort_order']);
        $Qlayout->bind_int(':page_specific', $data['page_specific'] === true ? '1' : '0');
        $Qlayout->set_logging($_SESSION['module'], $id);
        $Qlayout->execute();
        if (!$os_c_database->is_error()) {
            Os_C_cache::clear('templates_' . $set . '_layout');
            return true;
        }
        return false;
    }
    public function _delete($id, $set)
    {
        global $os_c_database;
        $Qdel = $os_c_database->query('delete from :table_templates_boxes_to_pages where id = :id');
        $Qdel->bind_table(':table_templates_boxes_to_pages', TABLE_TEMPLATES_BOXES_TO_PAGES);
        $Qdel->bind_int(':id', $id);
        $Qdel->set_logging($_SESSION['module'], $id);
        $Qdel->execute();
        if (!$os_c_database->is_error()) {
            Os_C_cache::clear('templates_' . $set . '_layout');
            return true;
        }
        return false;
    }
}