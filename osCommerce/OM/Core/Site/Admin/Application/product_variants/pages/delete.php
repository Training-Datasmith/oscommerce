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
echo osc_icon('trash.png') . ' ' . $os_c_object_info->get('title');
?></div>
<div class="infoBoxContent">
  <form name="paDelete" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&paID=' . $os_c_object_info->get('id') . '&action=delete');
?>" method="post">

<?php 
if ($os_c_object_info->get('total_products') > 0) {
    ?>

  <p><?php 
    echo '<b>' . sprintf($os_c_language->get('delete_error_attribute_group_in_use'), $os_c_object_info->get('total_products')) . '</b>';
    ?></p>

  <p align="center"><?php 
    echo '<input type="button" value="' . $os_c_language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
    ?></p>

<?php 
} else {
    $group_name = $os_c_object_info->get('title');
    if ($os_c_object_info->get('total_entries') > 0) {
        $group_name .= ' (' . sprintf($os_c_language->get('total_entries'), $os_c_object_info->get('total_entries')) . ')';
    }
    ?>

  <p><?php 
    echo $os_c_language->get('introduction_delete_attribute_group');
    ?></p>

  <p><?php 
    echo '<b>' . $group_name . '</b>';
    ?></p>

  <p align="center"><?php 
    echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
    ?></p>

<?php 
}
?>

  </form>
</div>
