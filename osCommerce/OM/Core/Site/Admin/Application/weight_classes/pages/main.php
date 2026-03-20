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
echo '<input type="button" value="' . $os_c_language->get('button_insert') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&action=save') . '\';" class="infoBoxButton" />';
?></p>

<?php 
$Qclasses = $os_c_database->query('select weight_class_id, weight_class_key, weight_class_title from :table_weight_classes where language_id = :language_id order by weight_class_title');
$Qclasses->bind_table(':table_weight_classes', TABLE_WEIGHT_CLASS);
$Qclasses->bind_int(':language_id', $os_c_language->get_id());
$Qclasses->set_batch_limit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
$Qclasses->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php 
echo $Qclasses->get_batch_total_pages($os_c_language->get('batch_results_number_of_entries'));
?></td>
    <td align="right"><?php 
echo $Qclasses->get_batch_page_links('page', $os_c_template->get_module(), false);
?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php 
echo $os_c_language->get('table_heading_weight_classes');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_unit');
?></th>
      <th width="150"><?php 
echo $os_c_language->get('table_heading_action');
?></th>
      <th align="center" width="20"><?php 
echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"');
?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="3"><?php 
echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $os_c_language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />';
?></th>
      <th align="center" width="20"><?php 
echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"');
?></th>
    </tr>
  </tfoot>
  <tbody>

<?php 
while ($Qclasses->next()) {
    $class_name = $Qclasses->value('weight_class_title');
    if ($Qclasses->value_int('weight_class_id') == SHIPPING_WEIGHT_UNIT) {
        $class_name .= ' (' . $os_c_language->get('default_entry') . ')';
    }
    ?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td onclick="document.getElementById('batch<?php 
    echo $Qclasses->value_int('weight_class_id');
    ?>').checked = !document.getElementById('batch<?php 
    echo $Qclasses->value_int('weight_class_id');
    ?>').checked;"><?php 
    echo $class_name;
    ?></td>
      <td><?php 
    echo $Qclasses->value('weight_class_key');
    ?></td>
      <td align="right">

<?php 
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&wcID=' . $Qclasses->value_int('weight_class_id') . '&action=save'), osc_icon('edit.png')) . '&nbsp;' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&wcID=' . $Qclasses->value_int('weight_class_id') . '&action=delete'), osc_icon('trash.png'));
    ?>

      </td>
      <td align="center"><?php 
    echo osc_draw_checkbox_field('batch[]', $Qclasses->value_int('weight_class_id'), null, 'id="batch' . $Qclasses->value_int('weight_class_id') . '"');
    ?></td>
    </tr>

<?php 
}
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php 
echo '<b>' . $os_c_language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $os_c_language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $os_c_language->get('icon_trash');
?></td>
    <td align="right"><?php 
echo $Qclasses->get_batch_pages_pull_down_menu('page', $os_c_template->get_module());
?></td>
  </tr>
</table>
