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
echo osc_icon('trash.png') . ' ' . $os_c_language->get('action_heading_batch_delete_image_groups');
?></div>
<div class="infoBoxContent">
  <form name="gDeleteBatch" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&action=batchDelete');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_batch_delete_image_groups');
?></p>

<?php 
$check_default_flag = false;
$Qgroups = $os_c_database->query('select id, title from :table_products_images_groups where id in (":id") and language_id = :language_id order by title');
$Qgroups->bind_table(':table_products_images_groups', TABLE_PRODUCTS_IMAGES_GROUPS);
$Qgroups->bind_raw(':id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
$Qgroups->bind_int(':language_id', $os_c_language->get_id());
$Qgroups->execute();
$names_string = '';
while ($Qgroups->next()) {
    if ($Qgroups->value('id') == DEFAULT_IMAGE_GROUP_ID) {
        $check_default_flag = true;
    }
    $names_string .= osc_draw_hidden_field('batch[]', $Qgroups->value_int('id')) . '<b>' . $Qgroups->value('title') . '</b>, ';
}
if (!empty($names_string)) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
}
echo '<p>' . $names_string . '</p>';
if ($check_default_flag === true) {
    echo '  <p><b>' . $os_c_language->get('batch_delete_error_image_group_prohibited') . '</b></p>';
    echo '  <p align="center"><input type="button" value="' . $os_c_language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
} else {
    echo '  <p align="center"><input type="submit" value="' . $os_c_language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
}
?>

  </form>
</div>
