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
include 'includes/modules/order_total/' . $_GET['module'] . '.php';
$os_c_language->inject_definitions('modules/order_total/' . $_GET['module'] . '.xml');
$module = 'osC_OrderTotal_' . $_GET['module'];
$module = new $module();
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
echo osc_icon('edit.png') . ' ' . $module->get_title();
?></div>
<div class="infoBoxContent">
  <form name="mEdit" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&module=' . $module->get_code() . '&action=save');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_edit_order_total_module');
?></p>

<?php 
$keys = '';
foreach ($module->get_keys() as $key) {
    $Qkey = $os_c_database->query('select configuration_title, configuration_value, configuration_description, use_function, set_function from :table_configuration where configuration_key = :configuration_key');
    $Qkey->bind_table(':table_configuration', TABLE_CONFIGURATION);
    $Qkey->bind_value(':configuration_key', $key);
    $Qkey->execute();
    $keys .= '<b>' . $Qkey->value('configuration_title') . '</b><br />' . $Qkey->value('configuration_description') . '<br />';
    if (!osc_empty($Qkey->value('set_function'))) {
        $keys .= osc_call_user_func($Qkey->value('set_function'), $Qkey->value('configuration_value'), $key);
    } else {
        $keys .= osc_draw_input_field('configuration[' . $key . ']', $Qkey->value('configuration_value'));
    }
    $keys .= '<br /><br />';
}
$keys = substr($keys, 0, strrpos($keys, '<br /><br />'));
?>

  <p><?php 
echo $keys;
?></p>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
