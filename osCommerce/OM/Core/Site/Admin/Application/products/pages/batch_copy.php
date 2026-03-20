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
echo osc_icon('copy.png') . ' ' . $os_c_language->get('action_heading_batch_copy_products');
?></div>
<div class="infoBoxContent">
  <form name="pBatchCopy" class="dataForm" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&cID=' . $_GET['cID'] . '&action=batch_copy');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_batch_copy_products');
?></p>

<?php 
$Qproducts = $os_c_database->query('select products_id, products_name from :table_products_description where products_id in (":products_id") and language_id = :language_id order by products_name');
$Qproducts->bind_table(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
$Qproducts->bind_raw(':products_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
$Qproducts->bind_int(':language_id', $os_c_language->get_id());
$Qproducts->execute();
$names_string = '';
while ($Qproducts->next()) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qproducts->value_int('products_id')) . '<b>' . $Qproducts->value('products_name') . '</b>, ';
}
if (!empty($names_string)) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
}
echo '<p>' . $names_string . '</p>';
?>

  <p><?php 
echo '<b>' . $os_c_language->get('field_categories') . '</b><br />' . osc_draw_pull_down_menu('new_category_id', $categories_array);
?></p>

  <p><?php 
echo '<b>' . $os_c_language->get('field_copy_method') . '</b><br />' . osc_draw_radio_field('copy_as', [['id' => 'link', 'text' => $os_c_language->get('copy_method_link')], ['id' => 'duplicate', 'text' => $os_c_language->get('copy_method_duplicate')]], 'link', null, '<br />');
?></p>

  <p align="center"><?php 
echo '<input type="submit" value="' . $os_c_language->get('button_copy') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&cID=' . $_GET['cID']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
