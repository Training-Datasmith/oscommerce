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
echo osc_icon('trash.png') . ' ' . $os_c_language->get('action_heading_batch_delete_attribute_groups');
?></div>
<div class="infoBoxContent">
  <form name="paDeleteBatch" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&action=batchDelete');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_batch_delete_attribute_groups');
?></p>

<?php 
$check_products_flag = [];
$Qgroups = $os_c_database->query('select id, title from :table_products_variants_groups where languages_id = :languages_id and id in (":id") order by title');
$Qgroups->bind_table(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
$Qgroups->bind_int(':languages_id', $os_c_language->get_id());
$Qgroups->bind_raw(':id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
$Qgroups->execute();
$names_string = '';
while ($Qgroups->next()) {
    $Qproducts = $os_c_database->query('select count(*) as total_products from :table_products_variants pv, :table_products_variants_values pvv where pvv.products_variants_groups_id = :products_variants_groups_id and pvv.id = pv.products_variants_values_id');
    $Qproducts->bind_table(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
    $Qproducts->bind_table(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
    $Qproducts->bind_int(':products_variants_groups_id', $Qgroups->value_int('id'));
    $Qproducts->execute();
    if ($Qproducts->value_int('total_products') > 0) {
        $check_products_flag[] = $Qgroups->value('products_options_name');
    }
    $Qentries = $os_c_database->query('select count(*) as total_entries from :table_products_variants_values where products_variants_groups_id = :products_variants_groups_id');
    $Qentries->bind_table(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
    $Qentries->bind_int(':products_variants_groups_id', $Qgroups->value_int('id'));
    $Qentries->execute();
    $group_name = $Qgroups->value('title');
    if ($Qentries->value_int('total_entries') > 0) {
        $group_name .= ' (' . sprintf($os_c_language->get('total_entries'), $Qentries->value_int('total_entries')) . ')';
    }
    $names_string .= osc_draw_hidden_field('batch[]', $Qgroups->value_int('id')) . '<b>' . $group_name . '</b>, ';
}
if (!empty($names_string)) {
    $names_string = substr($names_string, 0, -2);
}
echo '<p>' . $names_string . '</p>';
if (empty($check_products_flag)) {
    ?>

  <p align="center"><?php 
    echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
    ?></p>

<?php 
} else {
    ?>

  <p><b><?php 
    echo $os_c_language->get('batch_delete_error_attribute_groups_in_use');
    ?></b></p>

  <p><?php 
    echo implode(', ', $check_products_flag);
    ?></p>

  <p align="center"><?php 
    echo '<input type="button" value="' . $os_c_language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
    ?></p>

<?php 
}
?>

  </form>
</div>
