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
$os_c_directory_listing = new Os_C_directory_Listing('includes/modules/order_total');
$os_c_directory_listing->set_include_directories(false);
$files = $os_c_directory_listing->get_files();
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
echo $os_c_language->get('table_heading_order_total_modules');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_sort_order');
?></th>
      <th width="150"><?php 
echo $os_c_language->get('table_heading_action');
?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="3">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>

<?php 
$installed_modules = [];
foreach ($files as $file) {
    include 'includes/modules/order_total/' . $file['name'];
    $class = substr($file['name'], 0, strrpos($file['name'], '.'));
    if (class_exists('osC_OrderTotal_' . $class)) {
        $os_c_language->inject_definitions('modules/order_total/' . $class . '.xml');
        $module = 'osC_OrderTotal_' . $class;
        $module = new $module();
        ?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" <?php 
        echo $module->is_installed() && !$module->is_enabled() ? 'class="deactivatedRow"' : '';
        ?>>
      <td><?php 
        echo $module->get_title();
        ?></td>
      <td><?php 
        echo $module->get_sort_order();
        ?></td>
      <td align="right">

<?php 
        if ($module->is_installed()) {
            echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&module=' . $module->get_code() . '&action=save'), osc_icon('edit.png')) . '&nbsp;' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&module=' . $module->get_code() . '&action=uninstall'), osc_icon('uninstall.png'));
        } else {
            echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&module=' . $module->get_code() . '&action=install'), osc_icon('install.png'));
        }
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
echo '<b>' . $os_c_language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $os_c_language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('install.png') . '&nbsp;' . $os_c_language->get('icon_install') . '&nbsp;&nbsp;' . osc_icon('uninstall.png') . '&nbsp;' . $os_c_language->get('icon_uninstall');
?></td>
  </tr>
</table>
