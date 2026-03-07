<?php

declare(strict_types=1);
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

// HPDL  require('includes/applications/images/classes/images.php');
require('includes/classes/image.php');

class osC_Application_Images extends osC_Template_Admin
{
    /* Protected variables */

    protected $_module = 'images';
    protected $_page_title;
    protected $_page_contents = 'main.php';

    /* Class constructor */

    public function __construct()
    {
        global $osC_Language;

        $this->_page_title = $osC_Language->get('heading_title');

        if (!isset($_GET['action'])) {
            $_GET['action'] = '';
        }

        if (!isset($_GET['module'])) {
            $_GET['module'] = '';
        }

        if (!empty($_GET['module']) && !file_exists('includes/modules/image/' . $_GET['module'] . '.php')) {
            $_GET['module'] = '';
        }

        if (empty($_GET['module'])) {
            $this->_page_contents = 'listing.php';
        }
    }
}
