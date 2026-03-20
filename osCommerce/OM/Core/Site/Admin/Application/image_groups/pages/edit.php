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
$os_c_object_info = new Os_C_object_Info(Os_C_image_Groups_admin::get_data($_GET['gID']));
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
echo osc_icon('edit.png') . ' ' . $os_c_object_info->get('title');
?></div>
<div class="infoBoxContent">
  <form name="gEdit" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&gID=' . $os_c_object_info->get('id') . '&page=' . $_GET['page'] . '&action=save');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_edit_image_group');
?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_title') . '</b>';
?></td>
      <td width="60%">

<?php 
$status_name = [];
$Qgd = $os_c_database->query('select language_id, title from :table_products_images_groups where id = :id');
$Qgd->bind_table(':table_products_images_groups', TABLE_PRODUCTS_IMAGES_GROUPS);
$Qgd->bind_int(':id', $os_c_object_info->get('id'));
$Qgd->execute();
while ($Qgd->next()) {
    $status_name[$Qgd->value_int('language_id')] = $Qgd->value('title');
}
foreach ($os_c_language->get_all() as $l) {
    echo $os_c_language->show_image($l['code']) . '&nbsp;' . osc_draw_input_field('title[' . $l['id'] . ']', isset($status_name[$l['id']]) ? $status_name[$l['id']] : '') . '<br />';
}
?>

      </td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_code') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_input_field('code', $os_c_object_info->get('code'));
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_width') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_input_field('width', $os_c_object_info->get('size_width'));
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_height') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_input_field('height', $os_c_object_info->get('size_height'));
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_force_size') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_checkbox_field('force_size', null, $os_c_object_info->get('force_size') == '1');
?></td>
    </tr>

<?php 
if ($os_c_object_info->get('id') != DEFAULT_IMAGE_GROUP_ID) {
    ?>

    <tr>
      <td width="40%"><?php 
    echo '<b>' . $os_c_language->get('field_set_as_default') . '</b>';
    ?></td>
      <td width="60%"><?php 
    echo osc_draw_checkbox_field('default');
    ?></td>
    </tr>

<?php 
}
?>

  </table>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
