<?php

/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
$goto_array = [['id' => '', 'text' => $os_c_language->get('top_level')]];
if ($_SESSION['fm_directory'] != OSC_ADMIN_FILE_MANAGER_ROOT_PATH) {
    $path_array = explode('/', substr($_SESSION['fm_directory'], strlen(OSC_ADMIN_FILE_MANAGER_ROOT_PATH) + 1));
    foreach ($path_array as $value) {
        if (sizeof($goto_array) < 2) {
            $goto_array[] = ['id' => $value, 'text' => $value];
        } else {
            $parent = end($goto_array);
            $goto_array[] = ['id' => $parent['id'] . '/' . $value, 'text' => $parent['id'] . '/' . $value];
        }
    }
}
$os_c_directory_listing = new Os_C_directory_Listing($_SESSION['fm_directory']);
$os_c_directory_listing->set_stats(true);
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->size($os_c_template->get_module()) > 0) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<div style="float: right;">
  <form name="file_manager" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT);
?>" method="get"><?php 
echo osc_draw_hidden_field($os_c_template->get_module());
?>

  <?php 
echo $os_c_language->get('operation_heading_directory') . ' ' . osc_draw_pull_down_menu('goto', $goto_array, substr($_SESSION['fm_directory'], strlen(OSC_ADMIN_FILE_MANAGER_ROOT_PATH) + 1), 'onchange="this.form.submit();"');
?>

  <?php 
echo '<input type="button" value="' . $os_c_language->get('button_upload') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&action=upload') . '\';" class="infoBoxButton" />&nbsp;<input type="button" value="' . $os_c_language->get('button_new_file') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&action=save') . '\';" class="infoBoxButton" />&nbsp;<input type="button" value="' . $os_c_language->get('button_new_folder') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&action=saveDirectory') . '\';" class="infoBoxButton" />';
?>

  </form>
</div>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php 
echo $os_c_language->get('table_heading_files');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_size');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_permissions');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_user');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_group');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_writable');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_date_last_modified');
?></th>
      <th width="150"><?php 
echo $os_c_language->get('table_heading_action');
?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="8">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>

<?php 
if ($_SESSION['fm_directory'] != OSC_ADMIN_FILE_MANAGER_ROOT_PATH) {
    ?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td colspan="8"><?php 
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&goto=' . $goto_array[sizeof($goto_array) - 2]['id']), osc_icon('2uparrow.png') . '&nbsp;' . $os_c_language->get('parent_level'));
    ?></td>
    </tr>

<?php 
}
foreach ($os_c_directory_listing->get_files() as $file) {
    $file_owner = posix_getpwuid($file['user_id']);
    $group_owner = posix_getgrgid($file['group_id']);
    if ($file['is_directory'] === true) {
        $entry_icon = osc_icon('folder_red.png');
        $entry_url = osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&directory=' . $file['name']);
    } else {
        $entry_icon = osc_icon('file.png');
        $entry_url = osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&entry=' . $file['name'] . '&action=save');
    }
    ?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td><?php 
    echo osc_link_object($entry_url, $entry_icon . '&nbsp;' . $file['name']);
    ?></td>
      <td align="right"><?php 
    echo number_format($file['size']);
    ?></td>
      <td align="center"><tt><?php 
    echo osc_get_file_permissions($file['permissions']);
    ?></tt></td>
      <td><?php 
    echo $file_owner['name'];
    ?></td>
      <td><?php 
    echo $group_owner['name'];
    ?></td>
      <td align="center"><?php 
    echo osc_icon(is_writable($os_c_directory_listing->get_directory() . '/' . $file['name']) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null);
    ?></td>
      <td align="right"><?php 
    echo date('F d Y H:i:s', $file['last_modified']);
    ?></td>
      <td align="right">

<?php 
    if ($file['is_directory'] === false) {
        echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&entry=' . $file['name'] . '&action=save'), osc_icon('edit.png')) . '&nbsp;' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&entry=' . $file['name'] . '&action=download'), osc_icon('save.png')) . '&nbsp;';
    } else {
        echo osc_image('images/pixel_trans.gif') . '&nbsp;' . osc_image('images/pixel_trans.gif') . '&nbsp;';
    }
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&entry=' . $file['name'] . '&action=delete'), osc_icon('trash.png'));
    ?>

      </td>
    </tr>

<?php 
}
?>

  </tbody>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php 
echo '<b>' . $os_c_language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $os_c_language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('download.png') . '&nbsp;' . $os_c_language->get('icon_download') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $os_c_language->get('icon_trash');
?></td>
  </tr>
</table>

<p><?php 
echo osc_output_string_protected($_SESSION['fm_directory']);
?></p>
