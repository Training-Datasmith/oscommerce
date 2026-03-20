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
$os_c_object_info = new Os_C_object_Info(Os_C_product_Variants_admin::get_entry($_GET['paeID']));
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
echo osc_icon('trash.png') . ' ' . $os_c_object_info->get_protected('title');
?></div>
<div class="infoBoxContent">
  <form name="paeDelete" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '=' . $_GET[$os_c_template->get_module()] . '&page=' . $_GET['page'] . '&paeID=' . $os_c_object_info->get('id') . '&action=deleteEntry');
?>" method="post">

<?php 
if ($os_c_object_info->get_int('total_products') > 0) {
    ?>

  <p><?php 
    echo '<b>' . sprintf($os_c_language->get('delete_error_group_entry_in_use'), $os_c_object_info->get_int('total_products')) . '</b>';
    ?></p>

  <p align="center"><?php 
    echo '<input type="button" value="' . $os_c_language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '=' . $_GET[$os_c_template->get_module()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
    ?></p>

<?php 
} else {
    ?>

  <p><?php 
    echo $os_c_language->get('introduction_delete_group_entry');
    ?></p>

  <p><?php 
    echo '<b>' . $os_c_object_info->get_protected('title') . '</b>';
    ?></p>

  <p align="center"><?php 
    echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '=' . $_GET[$os_c_template->get_module()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
    ?></p>

<?php 
}
?>

  </form>
</div>
