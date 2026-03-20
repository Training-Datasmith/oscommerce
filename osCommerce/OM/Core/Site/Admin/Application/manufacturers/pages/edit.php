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
$os_c_object_info = new Os_C_object_Info(Os_C_manufacturers_admin::get_data($_GET['mID']));
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
echo osc_icon('edit.png') . ' ' . $os_c_object_info->get('manufacturers_name');
?></div>
<div class="infoBoxContent">
  <form name="mEdit" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&mID=' . $os_c_object_info->get('manufacturers_id') . '&action=save');
?>" method="post" enctype="multipart/form-data">

  <p><?php 
echo $os_c_language->get('introduction_edit_manufacturer');
?></p>

  <p><?php 
echo $os_c_language->get('field_name') . '<br />' . osc_draw_input_field('manufacturers_name', $os_c_object_info->get('manufacturers_name'));
?></p>
  <p><?php 
echo osc_image('../' . DIR_WS_IMAGES . 'manufacturers/' . $os_c_object_info->get('manufacturers_image'), $os_c_object_info->get('manufacturers_name')) . '<br />' . DIR_WS_CATALOG . DIR_WS_IMAGES . 'manufacturers/<br /><b>' . $os_c_object_info->get('manufacturers_image') . '</b>';
?></p>
  <p><?php 
echo $os_c_language->get('field_image') . '<br />' . osc_draw_file_field('manufacturers_image', true);
?></p>

  <p>

<?php 
echo $os_c_language->get('field_url');
$manufacturers_array = [];
$Qmanufacturer = $os_c_database->query('select manufacturers_url, languages_id from :table_manufacturers_info where manufacturers_id = :manufacturers_id');
$Qmanufacturer->bind_table(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
$Qmanufacturer->bind_int(':manufacturers_id', $os_c_object_info->get('manufacturers_id'));
$Qmanufacturer->execute();
while ($Qmanufacturer->next()) {
    $manufacturers_array[$Qmanufacturer->value_int('languages_id')] = $Qmanufacturer->value('manufacturers_url');
}
foreach ($os_c_language->get_all() as $l) {
    echo '<br />' . $os_c_language->show_image($l['code']) . '&nbsp;' . osc_draw_input_field('manufacturers_url[' . $l['id'] . ']', $manufacturers_array[$l['id']]);
}
?>

  </p>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
