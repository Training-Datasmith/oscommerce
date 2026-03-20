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
$os_c_object_info = new Os_C_object_Info(Os_C_administrators_Log_admin::get_data($_GET['lID']));
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
echo osc_icon('trash.png') . ' ' . $os_c_object_info->get('user_name') . ' &raquo; ' . $os_c_object_info->get('module_action') . ' &raquo; ' . $os_c_object_info->get('module') . ' &raquo; ' . $os_c_object_info->get('module_id');
?></div>
<div class="infoBoxContent">
  <form name="lDelete" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu'] . '&lID=' . $os_c_object_info->get('id') . '&action=delete');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_delete_entry');
?></p>

  <p><?php 
echo '<b>' . $os_c_object_info->get('user_name') . ' &raquo; ' . $os_c_object_info->get('module_action') . ' &raquo; ' . $os_c_object_info->get('module') . ' &raquo; ' . $os_c_object_info->get('module_id') . '</b>';
?></p>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
