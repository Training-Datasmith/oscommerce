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
echo osc_icon('trash.png') . ' ' . $os_c_language->get('action_heading_batch_delete_template_layout_modules');
?></div>
<div class="infoBoxContent">
  <form name="lDeleteBatch" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter'] . '&action=batchDelete');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_batch_delete_template_layout_modules');
?></p>

<?php 
$Qlayout = $os_c_database->query('select b2p.*, b.title as box_title from :table_templates_boxes_to_pages b2p, :table_templates_boxes b where b2p.id in (":id") and b2p.templates_boxes_id = b.id order by b.title');
$Qlayout->bind_table(':table_templates_boxes_to_pages', TABLE_TEMPLATES_BOXES_TO_PAGES);
$Qlayout->bind_table(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
$Qlayout->bind_raw(':id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
$Qlayout->execute();
$names_string = '';
while ($Qlayout->next()) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qlayout->value_int('id')) . '<b>' . $Qlayout->value('box_title') . '</b>, ';
}
if (!empty($names_string)) {
    $names_string = substr($names_string, 0, -2);
}
echo '<p>' . $names_string . '</p>';
?>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
