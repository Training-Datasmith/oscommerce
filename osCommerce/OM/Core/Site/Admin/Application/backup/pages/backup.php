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
$compression_array = [['id' => 'none', 'text' => $os_c_language->get('field_compression_none')]];
if (!osc_empty(CFG_APP_GZIP) && file_exists(CFG_APP_GZIP)) {
    $compression_array[] = ['id' => 'gzip', 'text' => $os_c_language->get('field_compression_gzip')];
}
if (!osc_empty(CFG_APP_ZIP) && file_exists(CFG_APP_ZIP)) {
    $compression_array[] = ['id' => 'zip', 'text' => $os_c_language->get('field_compression_zip')];
}
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
echo osc_icon('new.png') . ' ' . $os_c_language->get('action_heading_new_backup');
?></div>
<div class="infoBoxContent">
  <form name="bBackup" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&action=backup');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_new_backup');
?></p>

  <p><?php 
echo osc_draw_radio_field('compression', $compression_array, 'none', null, '<br />');
?></p>

  <p>

<?php 
if (!osc_empty(DIR_FS_BACKUP) && is_dir(DIR_FS_BACKUP) && is_writeable(DIR_FS_BACKUP)) {
    echo osc_draw_checkbox_field('download_only', [['id' => 'yes', 'text' => $os_c_language->get('field_download_only')]]);
} else {
    echo osc_draw_radio_field('download_only', [['id' => 'yes', 'text' => $os_c_language->get('field_download_only')]], true);
}
?>

  </p>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_backup') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
