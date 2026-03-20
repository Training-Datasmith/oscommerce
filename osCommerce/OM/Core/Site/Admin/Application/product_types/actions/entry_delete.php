<?php

declare (strict_types=1);
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
class Os_C_application_product_types_actions_entry_delete extends Os_C_application_product_types
{
    public function __construct()
    {
        global $os_c_message_stack;
        parent::__construct();
        $this->_page_contents = 'entries_delete.php';
        if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
            if (Os_C_product_Types_admin::delete_assignments($_GET[$this->_module], $_GET['aID'])) {
                $os_c_message_stack->add($this->_module, OSCOM::get_def('ms_success_action_performed'), 'success');
            } else {
                $os_c_message_stack->add($this->_module, OSCOM::get_def('ms_error_action_not_performed'), 'error');
            }
            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module]));
        }
    }
}