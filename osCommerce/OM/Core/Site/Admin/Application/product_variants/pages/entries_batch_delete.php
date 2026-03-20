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
echo osc_icon('trash.png') . ' ' . $os_c_language->get('action_heading_batch_delete_group_entries');
?></div>
<div class="infoBoxContent">
  <form name="paeDeleteBatch" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '=' . $_GET[$os_c_template->get_module()] . '&page=' . $_GET['page'] . '&action=batchDeleteEntries');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_batch_delete_group_entries');
?></p>

<?php 
$check_products_array = [];
$Qentries = $os_c_database->query('select id, title from :table_products_variants_values where id in (":id") and languages_id = :languages_id order by title');
$Qentries->bind_table(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
$Qentries->bind_raw(':id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
$Qentries->bind_int(':languages_id', $os_c_language->get_id());
$Qentries->execute();
$names_string = '';
while ($Qentries->next()) {
    $Qproducts = $os_c_database->query('select count(*) as total_products from :table_products_variants where products_variants_values_id = :products_variants_values_id');
    $Qproducts->bind_table(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
    $Qproducts->bind_int(':products_variants_values_id', $Qentries->value_int('id'));
    $Qproducts->execute();
    if ($Qproducts->value_int('total_products') > 0) {
        $check_products_array[] = $Qentries->value('title');
    }
    $names_string .= osc_draw_hidden_field('batch[]', $Qentries->value_int('id')) . '<b>' . $Qentries->value('title') . '</b>, ';
}
if (!empty($names_string)) {
    $names_string = substr($names_string, 0, -2);
}
echo '<p>' . $names_string . '</p>';
if (empty($check_products_array)) {
    ?>

  <p align="center"><?php 
    echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '=' . $_GET[$os_c_template->get_module()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
    ?></p>

<?php 
} else {
    ?>

  <p><b><?php 
    echo $os_c_language->get('batch_delete_error_group_entries_in_use');
    ?></b></p>

  <p><?php 
    echo implode(', ', $check_products_array);
    ?></p>

  <p align="center"><?php 
    echo '<input type="button" value="' . $os_c_language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '=' . $_GET[$os_c_template->get_module()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
    ?></p>

<?php 
}
?>

  </form>
</div>
