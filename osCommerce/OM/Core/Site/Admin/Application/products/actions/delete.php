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
class Os_C_application_products_actions_delete extends Os_C_application_products
{
    public function __construct()
    {
        global $os_c_language, $os_c_message_stack;
        parent::__construct();
        $this->_page_contents = 'delete.php';
        if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
            if (Os_C_products_admin::delete($_GET[$this->_module])) {
                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
            } else {
                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
            }
            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&cID=' . $_GET['cID']));
        }
    }
}