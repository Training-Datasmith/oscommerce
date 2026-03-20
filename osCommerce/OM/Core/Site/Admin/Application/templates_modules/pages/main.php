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
$os_c_directory_listing = new Os_C_directory_Listing('../includes/modules/' . $_GET['set']);
$os_c_directory_listing->set_include_directories(false);
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&set=' . $_GET['set']), $os_c_template->get_page_title());
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
    include '../includes/modules/' . $_GET['set'] . '/' . $file['name'];
    $code = substr($file['name'], 0, strrpos($file['name'], '.'));
    $class = 'osC_' . ucfirst($_GET['set']) . '_' . $code;
    if (class_exists($class)) {
        if (call_user_func([$class, 'isInstalled'], $code, $_GET['set']) === false) {
            $os_c_language->inject_definitions('modules/' . $_GET['set'] . '/' . $code . '.xml');
        }
        $module = new $class();
        ?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" <?php 
        echo $module->is_installed() && !$module->is_active() ? 'class="deactivatedRow"' : '';
        ?>>
      <td><?php 
        echo $module->get_title();
        ?></td>
      <td align="right">

<?php 
        if ($module->is_installed() && $module->is_active()) {
            if ($module->has_keys()) {
                echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&set=' . $_GET['set'] . '&module=' . $code . '&action=save'), osc_icon('edit.png')) . '&nbsp;';
            } else {
                echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;';
            }
            echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&set=' . $_GET['set'] . '&module=' . $code . '&action=uninstall'), osc_icon('uninstall.png')) . '&nbsp;';
        } else {
            echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&set=' . $_GET['set'] . '&module=' . $code . '&action=install'), osc_icon('install.png')) . '&nbsp;';
        }
        echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&set=' . $_GET['set'] . '&module=' . $code . '&action=info'), osc_icon('info.png'));
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
echo '<b>' . $os_c_language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $os_c_language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('install.png') . '&nbsp;' . $os_c_language->get('icon_install') . '&nbsp;&nbsp;' . osc_icon('uninstall.png') . '&nbsp;' . $os_c_language->get('button_uninstall') . '&nbsp;&nbsp;' . osc_icon('info.png') . '&nbsp;' . $os_c_language->get('icon_info');
?></td>
  </tr>
</table>

<p><?php 
echo $os_c_language->get('modules_location') . ' ' . $os_c_directory_listing->get_directory();
?></p>
