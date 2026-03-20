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
echo osc_icon('info.png') . ' ' . $module->get_title();
?></div>
<div class="infoBoxContent">
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td><?php 
echo $os_c_language->get('field_title');
?></td>
      <td><?php 
echo $module->get_title();
?></td>
    </tr>
    <tr>
      <td><?php 
echo $os_c_language->get('field_description');
?></td>
      <td><?php 
echo $module->get_description();
?></td>
    </tr>
    <tr>
      <td><?php 
echo $os_c_language->get('field_author');
?></td>
      <td><?php 
echo $module->get_author_name();
?> (<?php 
echo $module->get_author_address();
?>)</td>
    </tr>
  </table>

  <p align="center"><?php 
echo '<input type="button" value="' . $os_c_language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()) . '\';" class="operationButton" />';
?></p>
</div>
