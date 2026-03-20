<?php

/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
$os_c_object_info = new Os_C_object_Info(Os_C_reviews_admin::get_data($_GET['rID']));
$rating_array = [];
for ($i = 1; $i <= 5; $i++) {
    $rating_array[] = ['id' => $i, 'text' => ''];
}
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
echo osc_icon('edit.png') . ' ' . $os_c_object_info->get('products_name');
?></div>
<div class="infoBoxContent">
  <form name="review" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=save');
?>" method="post">

  <p><?php 
echo '<b>' . $os_c_language->get('field_product') . '</b><br />' . $os_c_object_info->get('products_name');
?></p>
  <p><?php 
echo '<b>' . $os_c_language->get('field_author') . '</b><br />' . osc_output_string_protected($os_c_object_info->get('customers_name'));
?></p>
  <p><?php 
echo '<b>' . $os_c_language->get('field_date_added') . '</b><br />' . Os_C_date_Time::get_short($os_c_object_info->get('date_added'));
?></p>
  <p><?php 
echo '<b>' . $os_c_language->get('field_review') . '</b><br />' . osc_draw_textarea_field('reviews_text', $os_c_object_info->get('reviews_text'));
?></p>
  <p><?php 
echo '<b>' . $os_c_language->get('field_rating') . '</b><br />' . $os_c_language->get('rating_bad') . '&nbsp;' . osc_draw_radio_field('reviews_rating', $rating_array, $os_c_object_info->get('reviews_rating')) . '&nbsp;' . $os_c_language->get('rating_good');
?></p>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
