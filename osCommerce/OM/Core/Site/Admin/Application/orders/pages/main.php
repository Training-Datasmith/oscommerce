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

<form name="search" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT);
?>" method="get"><?php 
echo osc_draw_hidden_field($os_c_template->get_module());
?>

<p align="right">

<?php 
echo $os_c_language->get('operation_heading_order_id') . ' ' . osc_draw_input_field('oID') . '&nbsp;' . $os_c_language->get('operation_heading_customer_id') . ' ' . osc_draw_input_field('cID') . '&nbsp;' . $os_c_language->get('operation_heading_filter_status') . osc_draw_pull_down_menu('status', array_merge([['id' => '', 'text' => $os_c_language->get('all_statuses')]], $orders_statuses)) . '<input type="submit" value="GO" class="operationButton" />';
?>

</p>

</form>

<?php 
$Qorders = $os_c_database->query('select o.orders_id, o.customers_ip_address, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, greatest(date_purchased, coalesce(last_modified, date_purchased)) as date_sort, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from :table_orders o, :table_orders_total ot, :table_orders_status s where o.orders_id = ot.orders_id and ot.class = "total" and o.orders_status = s.orders_status_id and s.language_id = :language_id');
if (isset($_GET['oID']) && is_numeric($_GET['oID'])) {
    $Qorders->append_query('and o.orders_id = :orders_id');
    $Qorders->bind_int(':orders_id', $_GET['oID']);
}
if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
    $Qorders->append_query('and o.customers_id = :customers_id');
    $Qorders->bind_int(':customers_id', $_GET['cID']);
}
if (isset($_GET['status']) && is_numeric($_GET['status'])) {
    $Qorders->append_query('and s.orders_status_id = :orders_status_id');
    $Qorders->bind_int(':orders_status_id', $_GET['status']);
}
$Qorders->append_query('order by date_sort desc');
$Qorders->bind_table(':table_orders', TABLE_ORDERS);
$Qorders->bind_table(':table_orders_total', TABLE_ORDERS_TOTAL);
$Qorders->bind_table(':table_orders_status', TABLE_ORDERS_STATUS);
$Qorders->bind_int(':language_id', $os_c_language->get_id());
$Qorders->set_batch_limit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
$Qorders->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php 
echo $Qorders->get_batch_total_pages($os_c_language->get('batch_results_number_of_entries'));
?></td>
    <td align="right"><?php 
echo $Qorders->get_batch_page_links('page', $os_c_template->get_module() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] : ''), false);
?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php 
echo $os_c_language->get('table_heading_customers');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_order_total');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_date_purchased');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_status');
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
      <th align="right" colspan="5"><?php 
echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $os_c_language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&action=batchDelete') . '\';" />';
?></th>
      <th align="center" width="20"><?php 
echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"');
?></th>
    </tr>
  </tfoot>
  <tbody>

<?php 
while ($Qorders->next()) {
    ?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td><?php 
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $Qorders->value_int('orders_id') . '&action=save'), osc_icon('orders.png') . '&nbsp;' . $Qorders->value_protected('customers_name'));
    ?></td>
      <td><?php 
    echo strip_tags($Qorders->value('order_total'));
    ?></td>
      <td><?php 
    echo Os_C_date_Time::get_short($Qorders->value('date_purchased'), true);
    ?></td>
      <td><?php 
    echo $Qorders->value('orders_status_name');
    ?></td>
      <td align="right">

<?php 
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $Qorders->value_int('orders_id') . '&action=save'), osc_icon('edit.png')) . '&nbsp;' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $Qorders->value_int('orders_id') . '&action=delete'), osc_icon('trash.png'));
    ?>

      </td>
      <td align="center"><?php 
    echo osc_draw_checkbox_field('batch[]', $Qorders->value_int('orders_id'), null, 'id="batch' . $Qorders->value_int('orders_id') . '"');
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
echo $Qorders->get_batch_pages_pull_down_menu('page', $os_c_template->get_module());
?></td>
  </tr>
</table>
