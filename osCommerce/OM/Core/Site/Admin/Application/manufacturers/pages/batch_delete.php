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
echo osc_icon('trash.png') . ' ' . $os_c_language->get('action_heading_batch_delete_manufacturers');
?></div>
<div class="infoBoxContent">
  <form name="mDeleteBatch" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&action=batchDelete');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_batch_delete_manufacturers');
?></p>

<?php 
$products_flag = false;
$Qmanufacturers = $os_c_database->query('select manufacturers_id, manufacturers_name from :table_manufacturers where manufacturers_id in (":manufacturers_id") order by manufacturers_name');
$Qmanufacturers->bind_table(':table_manufacturers', TABLE_MANUFACTURERS);
$Qmanufacturers->bind_raw(':manufacturers_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
$Qmanufacturers->execute();
$names_string = '';
while ($Qmanufacturers->next()) {
    $Qproducts = $os_c_database->query('select count(*) as products_count from :table_products where manufacturers_id = :manufacturers_id');
    $Qproducts->bind_table(':table_products', TABLE_PRODUCTS);
    $Qproducts->bind_int(':manufacturers_id', $Qmanufacturers->value_int('manufacturers_id'));
    $Qproducts->execute();
    $manufacturer_name = $Qmanufacturers->value_protected('manufacturers_name');
    if ($Qproducts->value_int('products_count') > 0) {
        if ($products_flag === false) {
            $products_flag = true;
        }
        $manufacturer_name .= ' (' . sprintf($os_c_language->get('total_entries'), $Qproducts->value_int('products_count')) . ')';
    }
    $names_string .= osc_draw_hidden_field('batch[]', $Qmanufacturers->value_int('manufacturers_id')) . '<b>' . $manufacturer_name . '</b>, ';
}
if (!empty($names_string)) {
    $names_string = substr($names_string, 0, -2);
}
echo '<p>' . $names_string . '</p>';
?>

  <p><?php 
echo osc_draw_checkbox_field('delete_image', null, true) . ' ' . $os_c_language->get('field_batch_delete_images');
?></p>

<?php 
if ($products_flag === true) {
    ?>

  <p><?php 
    echo osc_draw_checkbox_field('delete_products') . ' ' . $os_c_language->get('field_delete_products');
    ?></p>

<?php 
}
?>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
