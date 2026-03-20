<?php

declare (strict_types=1);
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
class Os_C_application_product_types_actions_save extends Os_C_application_product_types
{
    public function __construct()
    {
        global $os_c_message_stack;
        parent::__construct();
        if (isset($_GET['tID']) && is_numeric($_GET['tID'])) {
            $this->_page_contents = 'edit.php';
        } else {
            $this->_page_contents = 'new.php';
        }
        if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
            $data = ['title' => $_POST['title']];
            if (Os_C_product_Types_admin::save(isset($_GET['tID']) && is_numeric($_GET['tID']) ? $_GET['tID'] : null, $data)) {
                $os_c_message_stack->add($this->_module, OSCOM::get_def('ms_success_action_performed'), 'success');
            } else {
                $os_c_message_stack->add($this->_module, OSCOM::get_def('ms_error_action_not_performed'), 'error');
            }
            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
        }
    }
}