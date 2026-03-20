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
$Qlayout = $os_c_database->query('select b2p.*, b.title as box_title from :table_templates_boxes_to_pages b2p, :table_templates_boxes b where b2p.id = :id and b2p.templates_boxes_id = b.id');
$Qlayout->bind_table(':table_templates_boxes_to_pages', TABLE_TEMPLATES_BOXES_TO_PAGES);
$Qlayout->bind_table(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
$Qlayout->bind_int(':id', $_GET['lID']);
$Qlayout->execute();
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
echo osc_icon('trash.png') . ' ' . $Qlayout->value('box_title');
?></div>
<div class="infoBoxContent">
  <form name="lDelete" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter'] . '&lID=' . $Qlayout->value_int('id') . '&action=delete');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_delete_template_layout_module');
?></p>

  <p><?php 
echo '<b>' . $Qlayout->value('box_title') . '</b>';
?></p>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
