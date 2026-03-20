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
class Os_C_application_cache extends Os_C_template_admin
{
    /* Protected variables */
    protected $_module = 'cache';
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
        // check if the cache directory exists
        if (is_dir(DIR_FS_WORK)) {
            if (!is_writeable(DIR_FS_WORK)) {
                $os_c_message_stack->add('header', sprintf($os_c_language->get('ms_error_cache_directory_not_writable'), DIR_FS_WORK), 'error');
            }
        } else {
            $os_c_message_stack->add('header', sprintf($os_c_language->get('ms_error_cache_directory_non_existant'), DIR_FS_WORK), 'error');
        }
        if (!empty($_GET['action'])) {
            switch ($_GET['action']) {
                case 'delete':
                    /*HPDL
                                  if ( osC_Cache::clear($_GET['block']) ) {
                                    $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
                                  } else {
                                    $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
                                  }
                      */
                    Os_C_cache::clear($_GET['block']);
                    osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
                    break;
                case 'batchDelete':
                    if (isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch'])) {
                        foreach ($_POST['batch'] as $id) {
                            Os_C_cache::clear($id);
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
                    }
                    break;
            }
        }
    }
}