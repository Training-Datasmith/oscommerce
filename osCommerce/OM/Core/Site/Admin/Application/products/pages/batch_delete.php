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
echo osc_icon('trash.png') . ' ' . $os_c_language->get('action_heading_batch_delete_products');
?></div>
<div class="infoBoxContent">
  <form name="pDelete" class="dataForm" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&cID=' . $_GET['cID'] . '&action=batch_delete');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_batch_delete_products');
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

  <p align="center"><?php 
echo '<input type="submit" value="' . $os_c_language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&cID=' . $_GET['cID']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
