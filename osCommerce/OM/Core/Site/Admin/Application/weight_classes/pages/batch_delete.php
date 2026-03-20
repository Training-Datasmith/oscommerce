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
echo osc_icon('trash.png') . ' ' . $os_c_language->get('action_heading_batch_delete_weight_classes');
?></div>
<div class="infoBoxContent">
  <form name="wcDeleteBatch" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&action=batchDelete');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_batch_delete_weight_classes');
?></p>

<?php 
$check_default_flag = false;
$check_products_flag = false;
$Qclasses = $os_c_database->query('select weight_class_id, weight_class_title from :table_weight_class where weight_class_id in (":weight_class_id") and language_id = :language_id order by weight_class_title');
$Qclasses->bind_table(':table_weight_class', TABLE_WEIGHT_CLASS);
$Qclasses->bind_raw(':weight_class_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
$Qclasses->bind_int(':language_id', $os_c_language->get_id());
$Qclasses->execute();
$names_string = '';
while ($Qclasses->next()) {
    if ($Qclasses->value('weight_class_id') == SHIPPING_WEIGHT_UNIT) {
        $check_default_flag = true;
    }
    $Qproducts = $os_c_database->query('select count(*) as total from :table_products where products_weight_class = :products_weight_class');
    $Qproducts->bind_table(':table_products', TABLE_PRODUCTS);
    $Qproducts->bind_int(':products_weight_class', $Qclasses->value_int('weight_class_id'));
    $Qproducts->execute();
    if ($Qproducts->value_int('total') > 0) {
        $check_products_flag = true;
    }
    $names_string .= osc_draw_hidden_field('batch[]', $Qclasses->value_int('weight_class_id')) . '<b>' . $Qclasses->value('weight_class_title') . '</b>, ';
}
if (!empty($names_string)) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
}
echo '<p>' . $names_string . '</p>';
if ($check_default_flag === true || $check_products_flag === true) {
    if ($check_default_flag === true) {
        echo '  <p><b>' . $os_c_language->get('batch_delete_error_weight_class_prohibited') . '</b></p>';
    }
    if ($check_products_flag === true) {
        echo '  <p><b>' . sprintf($os_c_language->get('batch_delete_error_weight_class_in_use'), $Qproducts->value_int('total')) . '</b></p>';
    }
    echo '  <p align="center"><input type="button" value="' . $os_c_language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
} else {
    echo '  <p align="center"><input type="submit" value="' . $os_c_language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
}
?>

  </form>
</div>
