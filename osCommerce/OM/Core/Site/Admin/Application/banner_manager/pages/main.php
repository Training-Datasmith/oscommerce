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
$Qbanners = $os_c_database->query('select banners_id, banners_title, banners_group, status from :table_banners order by banners_title, banners_group');
$Qbanners->bind_table(':table_banners', TABLE_BANNERS);
$Qbanners->set_batch_limit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
$Qbanners->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php 
echo $Qbanners->get_batch_total_pages($os_c_language->get('batch_results_number_of_entries'));
?></td>
    <td align="right"><?php 
echo $Qbanners->get_batch_page_links('page', $os_c_template->get_module(), false);
?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php 
echo $os_c_language->get('table_heading_banners');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_group');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_statistics');
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
      <th align="right" colspan="4"><?php 
echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $os_c_language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />';
?></th>
      <th align="center" width="20"><?php 
echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"');
?></th>
    </tr>
  </tfoot>
  <tbody>

<?php 
while ($Qbanners->next()) {
    $Qstats = $os_c_database->query('select sum(banners_shown) as banners_shown, sum(banners_clicked) as banners_clicked from :table_banners_history where banners_id = :banners_id');
    $Qstats->bind_table(':table_banners_history', TABLE_BANNERS_HISTORY);
    $Qstats->bind_int(':banners_id', $Qbanners->value_int('banners_id'));
    $Qstats->execute();
    ?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" <?php 
    echo $Qbanners->value_int('status') !== 1 ? 'class="deactivatedRow"' : '';
    ?>>
      <td onclick="document.getElementById('batch<?php 
    echo $Qbanners->value_int('banners_id');
    ?>').checked = !document.getElementById('batch<?php 
    echo $Qbanners->value_int('banners_id');
    ?>').checked;"><?php 
    echo $Qbanners->value('banners_title');
    ?></td>
      <td><?php 
    echo $Qbanners->value_protected('banners_group');
    ?></td>
      <td><?php 
    echo $Qstats->value_int('banners_shown') . ' / ' . $Qstats->value_int('banners_clicked');
    ?></td>
      <td align="right">

<?php 
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&bID=' . $Qbanners->value_int('banners_id') . '&action=preview'), osc_icon('banner_preview.png')) . '&nbsp;' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&bID=' . $Qbanners->value_int('banners_id') . '&action=statistics'), osc_icon('statistics.png')) . '&nbsp;' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&bID=' . $Qbanners->value_int('banners_id') . '&action=save'), osc_icon('edit.png')) . '&nbsp;' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&bID=' . $Qbanners->value_int('banners_id') . '&action=delete'), osc_icon('trash.png'));
    ?>

      </td>
      <td align="center"><?php 
    echo osc_draw_checkbox_field('batch[]', $Qbanners->value_int('banners_id'), null, 'id="batch' . $Qbanners->value_int('banners_id') . '"');
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
echo '<b>' . $os_c_language->get('table_action_legend') . '</b> ' . osc_icon('banner_preview.png') . '&nbsp;' . $os_c_language->get('icon_banner_preview') . '&nbsp;&nbsp;' . osc_icon('statistics.png') . '&nbsp;' . $os_c_language->get('icon_statistics') . '&nbsp;&nbsp;' . osc_icon('edit.png') . '&nbsp;' . $os_c_language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $os_c_language->get('icon_trash');
?></td>
    <td align="right"><?php 
echo $Qbanners->get_batch_pages_pull_down_menu('page', $os_c_template->get_module());
?></td>
  </tr>
</table>
