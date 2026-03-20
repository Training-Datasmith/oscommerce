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
$os_c_directory_listing = new Os_C_directory_Listing('includes/modules/image');
$os_c_directory_listing->set_include_directories(false);
$os_c_directory_listing->set_check_extension('php');
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->size($os_c_template->get_module()) > 0) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php 
echo $os_c_language->get('table_heading_modules');
?></th>
      <th width="150"><?php 
echo $os_c_language->get('table_heading_action');
?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="2">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>

<?php 
foreach ($os_c_directory_listing->get_files() as $file) {
    include 'includes/modules/image/' . $file['name'];
    $class = 'osC_Image_Admin_' . substr($file['name'], 0, strrpos($file['name'], '.'));
    if (class_exists($class)) {
        $module = new $class();
        ?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td><?php 
        echo $module->get_title();
        ?></td>
      <td align="right">

<?php 
        echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&module=' . substr($file['name'], 0, strrpos($file['name'], '.'))), osc_icon('run.png'));
        ?>

      </td>
    </tr>

<?php 
    }
}
?>

  </tbody>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php 
echo '<b>' . $os_c_language->get('table_action_legend') . '</b> ' . osc_icon('run.png') . '&nbsp;' . $os_c_language->get('icon_run');
?></td>
  </tr>
</table>
