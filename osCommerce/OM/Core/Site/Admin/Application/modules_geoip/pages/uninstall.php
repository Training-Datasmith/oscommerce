<?php

/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
include 'includes/modules/geoip/' . $_GET['module'] . '.php';
//HPDL  $osC_Language->injectDefinitions('modules/geoip/' . $_GET['module'] . '.xml');
$os_c_language->load_ini_file('modules/geoip/' . $_GET['module'] . '.php');
$module = 'osC_GeoIP_' . $_GET['module'];
$module = new $module();
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->size($os_c_template->get_module()) > 0) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<div class="infoBoxHeading"><?php 
echo osc_icon('uninstall.png') . ' ' . $module->get_title();
?></div>
<div class="infoBoxContent">
  <form name="mUninstall" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&module=' . $module->get_code() . '&action=uninstall');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_uninstall_geoip_module');
?></p>

  <p><?php 
echo '<b>' . $module->get_title() . '</b>';
?></p>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_uninstall') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
