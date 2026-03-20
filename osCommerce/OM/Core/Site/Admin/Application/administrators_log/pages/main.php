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
$modules_array = [['id' => '', 'text' => $os_c_language->get('filter_all')]];
foreach ($_SESSION[OSCOM::get_site()]['access'] as $module) {
    $modules_array[] = ['id' => $module, 'text' => $module];
}
$admins_array = [['id' => '', 'text' => $os_c_language->get('filter_all')]];
$Qadmins = $os_c_database->query('select id, user_name from :table_administrators order by user_name');
$Qadmins->bind_table(':table_administrators', TABLE_ADMINISTRATORS);
$Qadmins->execute();
while ($Qadmins->next()) {
    $admins_array[] = ['id' => $Qadmins->value_int('id'), 'text' => $Qadmins->value_protected('user_name')];
}
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->size($os_c_template->get_module()) > 0) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<div align="right">
  <form name="filter" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT);
?>" method="get"><?php 
echo osc_draw_hidden_field($os_c_template->get_module());
?>

  <?php 
echo $os_c_language->get('operation_title_filter_modules') . ' ' . osc_draw_pull_down_menu('fm', $modules_array);
?>
  <?php 
echo $os_c_language->get('operation_title_filter_users') . ' ' . osc_draw_pull_down_menu('fu', $admins_array);
?>

  <input type="submit" value="GO" class="operationButton" />

  </form>
</div>

<?php 
$Qlog = $os_c_database->query('select SQL_CALC_FOUND_ROWS count(al.id) as total, al.id, al.module, al.module_action, al.module_id, al.action, a.user_name, unix_timestamp(al.datestamp) as datestamp from :table_administrators_log al, :table_administrators a where');
if (!empty($_GET['fm']) && in_array($_GET['fm'], $_SESSION[OSCOM::get_site()]['access'])) {
    $Qlog->append_query('al.module = :module');
    $Qlog->bind_value(':module', $_GET['fm']);
} else {
    $Qlog->append_query('al.module in (":modules")');
    $Qlog->bind_raw(':modules', implode('", "', $_SESSION[OSCOM::get_site()]['access']));
}
$Qlog->append_query('and');
if (is_numeric($_GET['fu'])) {
    $Qlog->append_query('al.administrators_id = :administrators_id and');
    $Qlog->bind_int(':administrators_id', $_GET['fu']);
}
$Qlog->append_query('al.administrators_id = a.id group by al.id order by al.id desc');
$Qlog->bind_table(':table_administrators_log', TABLE_ADMINISTRATORS_LOG);
$Qlog->bind_table(':table_administrators', TABLE_ADMINISTRATORS);
$Qlog->set_batch_limit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
$Qlog->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php 
echo $Qlog->get_batch_total_pages($os_c_language->get('batch_results_number_of_entries'));
?></td>
    <td align="right"><?php 
echo $Qlog->get_batch_page_links('page', $os_c_template->get_module() . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu'], false);
?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php 
echo $os_c_language->get('table_heading_module');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_id');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_type');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_user');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_date');
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
      <th align="right" colspan="6"><?php 
echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $os_c_language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu'] . '&action=batchDelete') . '\';" />';
?></th>
      <th align="center" width="20"><?php 
echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"');
?></th>
    </tr>
  </tfoot>
  <tbody>

<?php 
while ($Qlog->next()) {
    ?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td onclick="document.getElementById('batch<?php 
    echo $Qlog->value_int('id');
    ?>').checked = !document.getElementById('batch<?php 
    echo $Qlog->value_int('id');
    ?>').checked;"><?php 
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu'] . '&lID=' . $Qlog->value_int('id') . '&action=info'), osc_icon('folder.png') . '&nbsp;' . $Qlog->value('module') . ' (' . $Qlog->value_int('total') . ')');
    ?></td>
      <td align="center"><?php 
    echo $Qlog->value_int('module_id');
    ?></td>
      <td align="center"><?php 
    echo $Qlog->value_protected('module_action');
    ?></td>
      <td align="right"><?php 
    echo $Qlog->value_protected('user_name');
    ?></td>
      <td align="right"><?php 
    echo date('d M Y H:i:s', $Qlog->value('datestamp'));
    ?></td>
      <td align="right">

<?php 
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu'] . '&lID=' . $Qlog->value_int('id') . '&action=info'), osc_icon('info.png')) . '&nbsp;' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu'] . '&lID=' . $Qlog->value_int('id') . '&action=delete'), osc_icon('trash.png'));
    ?>

      </td>
      <td align="center"><?php 
    echo osc_draw_checkbox_field('batch[]', $Qlog->value_int('id'), null, 'id="batch' . $Qlog->value_int('id') . '"');
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
echo '<b>' . $os_c_language->get('table_action_legend') . '</b> ' . osc_icon('info.png') . '&nbsp;' . $os_c_language->get('icon_info') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $os_c_language->get('icon_trash');
?></td>
    <td align="right"><?php 
echo $Qlog->get_batch_pages_pull_down_menu('page', $os_c_template->get_module() . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu']);
?></td>
  </tr>
</table>
