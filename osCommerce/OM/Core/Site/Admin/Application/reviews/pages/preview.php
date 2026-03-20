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
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->size($os_c_template->get_module()) > 0) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<p align="right"><?php 
echo '<input type="button" value="' . $os_c_language->get('button_back') . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';">';
?></p>

<p><?php 
echo '<b>' . $os_c_language->get('field_product') . '</b> ' . $os_c_object_info->get('products_name') . '<br /><b>' . $os_c_language->get('field_author') . '</b> ' . osc_output_string_protected($os_c_object_info->get('customers_name')) . '<br /><br /><b>' . $os_c_language->get('field_date_added') . '</b> ' . Os_C_date_Time::get_short($os_c_object_info->get('date_added'));
?></p>

<p><?php 
echo '<b>' . $os_c_language->get('field_review') . '</b><br />' . nl2br(osc_output_string_protected($os_c_object_info->get('reviews_text')));
?></p>

<p><?php 
echo '<b>' . $os_c_language->get('field_rating') . '</b>&nbsp;' . osc_image('../images/stars_' . $os_c_object_info->get('reviews_rating') . '.png', sprintf($os_c_language->get('rating_from_5_stars'), $os_c_object_info->get('reviews_rating'))) . '&nbsp;[' . sprintf($os_c_language->get('rating_from_5_stars'), $os_c_object_info->get('reviews_rating')) . ']';
?></p>

<?php 
if (defined('SERVICE_REVIEW_ENABLE_MODERATION') && SERVICE_REVIEW_ENABLE_MODERATION != -1) {
    echo '<p align="right"><input type="button" value="' . $os_c_language->get('button_approve') . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=rApprove') . '\';"> <input type="button" value="' . $os_c_language->get('button_reject') . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=rReject') . '\';"></p>';
}