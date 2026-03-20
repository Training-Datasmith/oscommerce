<?php

/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
$os_c_object_info = new Os_C_object_Info(Os_C_products_admin::get($_GET[$os_c_template->get_module()]));
$in_categories = [];
$Qcategories = $os_c_database->query('select categories_id from :table_products_to_categories where products_id = :products_id');
$Qcategories->bind_table(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
$Qcategories->bind_int(':products_id', $os_c_object_info->get_int('products_id'));
$Qcategories->execute();
while ($Qcategories->next()) {
    $in_categories[] = $Qcategories->value_int('categories_id');
}
$in_categories_path = '';
foreach ($in_categories as $category_id) {
    $in_categories_path .= $os_c_category_tree->get_path($category_id, 0, ' &raquo; ') . '<br />';
}
if (!empty($in_categories_path)) {
    $in_categories_path = substr($in_categories_path, 0, -6);
}
$categories_array = [['id' => '0', 'text' => $os_c_language->get('top_category')]];
foreach ($os_c_category_tree->get_array() as $value) {
    $categories_array[] = ['id' => $value['id'], 'text' => $value['title']];
}
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->exists($os_c_template->get_module())) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<div class="infoBoxHeading"><?php 
echo osc_icon('copy.png') . ' ' . $os_c_object_info->get_protected('products_name');
?></div>
<div class="infoBoxContent">
  <form name="pCopy" class="dataForm" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '=' . $os_c_object_info->get('products_id') . '&cID=' . $_GET['cID'] . '&action=copy');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_copy_product');
?></p>

  <fieldset>
    <p><?php 
echo '<b>' . $os_c_language->get('field_current_categories') . '</b><br />' . $in_categories_path;
?></p>

    <div><label for="new_category_id"><?php 
echo $os_c_language->get('field_categories');
?></label><?php 
echo osc_draw_pull_down_menu('new_category_id', $categories_array);
?></div>
    <div><label for="copy_as"><?php 
echo $os_c_language->get('field_copy_method');
?></label><?php 
echo osc_draw_radio_field('copy_as', [['id' => 'link', 'text' => $os_c_language->get('copy_method_link')], ['id' => 'duplicate', 'text' => $os_c_language->get('copy_method_duplicate')]], 'link', null, '<br />');
?></div>
  </fieldset>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_copy') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&cID=' . $_GET['cID']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
