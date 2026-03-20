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
$os_c_object_info = new Os_C_object_Info(Os_C_products_admin::get($_GET[$os_c_template->get_module()]));
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
echo osc_icon('trash.png') . ' ' . $os_c_object_info->get_protected('products_name');
?></div>
<div class="infoBoxContent">
  <form name="pDelete" class="dataForm" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '=' . $os_c_object_info->get_int('products_id') . '&cID=' . $_GET['cID'] . '&action=delete');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_delete_product');
?></p>

  <p><?php 
echo '<b>' . $os_c_object_info->get_protected('products_name') . '</b>';
?></p>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&cID=' . $_GET['cID']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
