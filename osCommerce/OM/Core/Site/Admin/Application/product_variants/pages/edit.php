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
$modules_array = [];
$os_c_directory_listing = new Os_C_directory_Listing('../includes/modules/variants');
$os_c_directory_listing->set_include_directories(false);
$os_c_directory_listing->set_check_extension('php');
foreach ($os_c_directory_listing->get_files() as $file) {
    $module = substr($file['name'], 0, strrpos($file['name'], '.'));
    $modules_array[] = ['id' => $module, 'text' => $module];
}
$os_c_object_info = new Os_C_object_Info(Os_C_product_Variants_admin::get_data($_GET['paID']));
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
  <form name="paEdit" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&paID=' . $os_c_object_info->get('id') . '&action=save');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_edit_attribute_group');
?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%" valign="top"><?php 
echo '<b>' . $os_c_language->get('field_group_name') . '</b>';
?></td>
      <td width="60%">

<?php 
$Qgd = $os_c_database->query('select languages_id, title from :table_products_variants_groups where id = :id');
$Qgd->bind_table(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
$Qgd->bind_int(':id', $os_c_object_info->get('id'));
$Qgd->execute();
$group_names = [];
while ($Qgd->next()) {
    $group_names[$Qgd->value_int('languages_id')] = $Qgd->value('title');
}
foreach ($os_c_language->get_all() as $l) {
    echo $os_c_language->show_image($l['code']) . '&nbsp;' . osc_draw_input_field('group_name[' . $l['id'] . ']', isset($group_names[$l['id']]) ? $group_names[$l['id']] : null) . '<br />';
}
?>

      </td>
    </tr>
    <tr>
      <td width="40%" valign="top"><?php 
echo '<b>' . $os_c_language->get('field_display_module') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_pull_down_menu('module', $modules_array, $os_c_object_info->get('module'));
?></td>
    </tr>
    <tr>
      <td width="40%" valign="top"><?php 
echo '<b>' . $os_c_language->get('field_sort_order') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_input_field('sort_order', $os_c_object_info->get('sort_order'));
?></td>
    </tr>
  </table>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
