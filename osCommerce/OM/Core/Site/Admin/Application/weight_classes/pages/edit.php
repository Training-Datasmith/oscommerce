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
$os_c_object_info = new Os_C_object_Info(Os_C_weight_Classes_admin::get_data($_GET['wcID']));
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
echo osc_icon('edit.png') . ' ' . $os_c_object_info->get('weight_class_title');
?></div>
<div class="infoBoxContent">
  <form name="wcEdit" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&wcID=' . $os_c_object_info->get('weight_class_id') . '&action=save');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_edit_weight_class');
?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_title_and_code') . '</b>';
?></td>
      <td width="60%">

<?php 
$Qwc = $os_c_database->query('select language_id, weight_class_key, weight_class_title from :table_weight_classes where weight_class_id = :weight_class_id');
$Qwc->bind_table(':table_weight_classes', TABLE_WEIGHT_CLASS);
$Qwc->bind_int(':weight_class_id', $os_c_object_info->get('weight_class_id'));
$Qwc->execute();
$classes_array = [];
while ($Qwc->next()) {
    $classes_array[$Qwc->value_int('language_id')] = ['key' => $Qwc->value('weight_class_key'), 'title' => $Qwc->value('weight_class_title')];
}
foreach ($os_c_language->get_all() as $l) {
    echo $os_c_language->show_image($l['code']) . '&nbsp;' . osc_draw_input_field('name[' . $l['id'] . ']', $classes_array[$l['id']]['title']) . osc_draw_input_field('key[' . $l['id'] . ']', $classes_array[$l['id']]['key'], 'size="4"') . '<br />';
}
?>

      </td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_rules') . '</b>';
?></td>
      <td width="60%">
        <table border="0" cellspacing="0" cellpadding="2">

<?php 
$Qrules = $os_c_database->query('select r.weight_class_to_id, r.weight_class_rule, c.weight_class_title from :table_weight_classes_rules r, :table_weight_classes c where r.weight_class_from_id = :weight_class_from_id and r.weight_class_to_id != :weight_class_to_id and r.weight_class_to_id = c.weight_class_id and c.language_id = :language_id order by c.weight_class_title');
$Qrules->bind_table(':table_weight_classes_rules', TABLE_WEIGHT_CLASS_RULES);
$Qrules->bind_table(':table_weight_classes', TABLE_WEIGHT_CLASS);
$Qrules->bind_int(':weight_class_from_id', $os_c_object_info->get('weight_class_id'));
$Qrules->bind_int(':weight_class_to_id', $os_c_object_info->get('weight_class_id'));
$Qrules->bind_int(':language_id', $os_c_language->get_id());
$Qrules->execute();
while ($Qrules->next()) {
    ?>

          <tr>
            <td><?php 
    echo $Qrules->value('weight_class_title') . ':';
    ?></td>
            <td><?php 
    echo osc_draw_input_field('rules[' . $Qrules->value_int('weight_class_to_id') . ']', $Qrules->value('weight_class_rule'));
    ?></td>
          </tr>

<?php 
}
?>

        </table>
      </td>
    </tr>

<?php 
if ($os_c_object_info->get('weight_class_id') != SHIPPING_WEIGHT_UNIT) {
    ?>

    <tr>
      <td width="40%"><?php 
    echo '<b>' . $os_c_language->get('field_set_as_default') . '</b>';
    ?></td>
      <td width="60%"><?php 
    echo osc_draw_checkbox_field('default');
    ?></td>
    </tr>

<?php 
}
?>

  </table>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
