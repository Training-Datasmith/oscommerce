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
$os_c_directory_listing = new Os_C_directory_Listing(DIR_FS_BACKUP);
$os_c_directory_listing->set_include_directories(false);
$os_c_directory_listing->set_check_extension('zip');
$os_c_directory_listing->set_check_extension('sql');
$os_c_directory_listing->set_check_extension('gz');
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->size($os_c_template->get_module()) > 0) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<p align="right"><?php 
echo '<input type="button" value="' . $os_c_language->get('button_backup') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&action=backup') . '\';" class="infoBoxButton" />&nbsp;<input type="button" value="' . $os_c_language->get('button_restore') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&action=restoreLocal') . '\';" class="infoBoxButton" />';
?></p>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php 
echo $os_c_language->get('table_heading_backups');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_date');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_file_size');
?></th>
      <th width="150"><?php 
echo $os_c_language->get('table_heading_action');
?></th>
      <th align="center" width="20"><?php 
echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"');
?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="4"><?php 
echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $os_c_language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&action=batchDelete') . '\';" />';
?></th>
      <th align="center" width="20"><?php 
echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"');
?></th>
    </tr>
  </tfoot>
  <tbody>

<?php 
foreach ($os_c_directory_listing->get_files() as $file) {
    ?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td><?php 
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&file=' . $file['name'] . '&action=download'), osc_icon('download.png') . '&nbsp;' . $file['name']);
    ?></td>
      <td><?php 
    echo Os_C_date_Time::get_short(Os_C_date_Time::from_unix_timestamp(filemtime(DIR_FS_BACKUP . $file['name'])), true);
    ?></td>
      <td><?php 
    echo number_format(filesize(DIR_FS_BACKUP . $file['name']));
    ?> bytes</td>
      <td align="right">

<?php 
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&file=' . $file['name'] . '&action=restore'), osc_icon('restore.png')) . '&nbsp;' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&file=' . $file['name'] . '&action=delete'), osc_icon('trash.png'));
    ?>

      </td>
      <td align="center"><?php 
    echo osc_draw_checkbox_field('batch[]', $file['name'], null, 'id="batch' . addslashes($file['name']) . '"');
    ?></td>
    </tr>

<?php 
}
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php 
echo '<b>' . $os_c_language->get('table_action_legend') . '</b> ' . osc_icon('download.png') . '&nbsp;' . $os_c_language->get('icon_download') . '&nbsp;&nbsp;' . osc_icon('restore.png') . '&nbsp;' . $os_c_language->get('icon_restore') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $os_c_language->get('icon_trash');
?></td>
  </tr>
</table>

<p><?php 
echo $os_c_language->get('backup_location') . ' ' . DIR_FS_BACKUP;
?></p>

<?php 
if (defined('DB_LAST_RESTORE')) {
    ?>

<p><?php 
    echo $os_c_language->get('last_restoration_date') . ' ' . DB_LAST_RESTORE . ' ' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&action=forget'), $os_c_language->get('forget_restoration_date'));
    ?></p>

<?php 
}