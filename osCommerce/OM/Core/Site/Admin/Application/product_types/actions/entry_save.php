<?php

declare (strict_types=1);
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
class Os_C_application_product_types_actions_entry_save extends Os_C_application_product_types
{
    public function __construct()
    {
        global $os_c_message_stack;
        parent::__construct();
        if (isset($_GET['aID']) && !empty($_GET['aID'])) {
            $this->_page_contents = 'entries_edit.php';
        } else {
            $this->_page_contents = 'entries_new.php';
            if (sizeof(Os_C_product_Types_admin::get_actions($_GET[$this->_module])) < 1) {
                $os_c_message_stack->add($this->_module, OSCOM::get_def('ms_warning_no_available_actions'), 'warning');
                $this->_page_contents = 'entries.php';
            }
        }
        if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
            $data = ['modules' => explode(',', $_POST['modules'])];
            if (Os_C_product_Types_admin::save_assignments($_GET[$this->_module], isset($_GET['aID']) ? $_GET['aID'] : $_POST['action'], $data)) {
                $os_c_message_stack->add($this->_module, OSCOM::get_def('ms_success_action_performed'), 'success');
            } else {
                $os_c_message_stack->add($this->_module, OSCOM::get_def('ms_error_action_not_performed'), 'error');
            }
            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module]));
        }
    }
}