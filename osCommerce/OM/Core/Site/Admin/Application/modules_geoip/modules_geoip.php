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
class Os_C_application_modules_geoip extends Os_C_template_admin
{
    /* Protected variables */
    protected $_module = 'modules_geoip';
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
        include 'includes/classes/geoip.php';
        if (!empty($_GET['action'])) {
            switch ($_GET['action']) {
                case 'info':
                    $this->_page_contents = 'info.php';
                    break;
                case 'save':
                    $this->_page_contents = 'edit.php';
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        $data = ['configuration' => $_POST['configuration']];
                        if ($this->_save($data)) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
                    }
                    break;
                case 'install':
                    if ($this->_install($_GET['module'])) {
                        $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                    } else {
                        $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                    }
                    osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
                    break;
                case 'uninstall':
                    $this->_page_contents = 'uninstall.php';
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        if ($this->_uninstall($_GET['module'])) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
                    }
                    break;
            }
        }
    }
    /* Private methods */
    public function _save($data)
    {
        global $os_c_database;
        $error = false;
        $os_c_database->start_transaction();
        foreach ($data['configuration'] as $key => $value) {
            $Qupdate = $os_c_database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
            $Qupdate->bind_table(':table_configuration', TABLE_CONFIGURATION);
            $Qupdate->bind_value(':configuration_value', is_array($data['configuration'][$key]) ? implode(',', $data['configuration'][$key]) : $value);
            $Qupdate->bind_value(':configuration_key', $key);
            $Qupdate->set_logging($_SESSION['module']);
            $Qupdate->execute();
            if ($os_c_database->is_error()) {
                $error = true;
                break;
            }
        }
        if ($error === false) {
            $os_c_database->commit_transaction();
            Os_C_cache::clear('configuration');
            return true;
        }
        $os_c_database->rollback_transaction();
        return false;
    }
    public function _install($key)
    {
        global $os_c_database, $os_c_language;
        if (file_exists('includes/modules/geoip/' . $key . '.php')) {
            //HPDL        $osC_Language->injectDefinitions('modules/geoip/' . $key . '.xml');
            $os_c_language->load_ini_file('modules/geoip/' . $key . '.php');
            include 'includes/modules/geoip/' . $key . '.php';
            $module = 'osC_GeoIP_' . $key;
            $module = new $module();
            $module->install();
            Os_C_cache::clear('modules-geoip');
            Os_C_cache::clear('configuration');
            return true;
        }
        return false;
    }
    public function _uninstall($key)
    {
        global $os_c_database, $os_c_language;
        if (file_exists('includes/modules/geoip/' . $key . '.php')) {
            //HPDL        $osC_Language->injectDefinitions('modules/geoip/' . $key . '.xml');
            $os_c_language->load_ini_file('modules/geoip/' . $key . '.php');
            include 'includes/modules/geoip/' . $key . '.php';
            $module = 'osC_GeoIP_' . $key;
            $module = new $module();
            $module->remove();
            Os_C_cache::clear('modules-geoip');
            Os_C_cache::clear('configuration');
            return true;
        }
        return false;
    }
}