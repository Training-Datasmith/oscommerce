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
$os_c_currencies = new Os_C_currencies();
$os_c_tax = new Os_C_tax_admin();
$os_c_weight = new Os_C_weight();
$os_c_geo_ip = Os_C_geo_Ip_admin::load();
if ($os_c_geo_ip->is_installed()) {
    $os_c_geo_ip->activate();
}
$xx_mins_ago = time() - 900;
// remove entries that have expired
$Qdelete = $os_c_database->query('delete from :table_whos_online where time_last_click < :time_last_click');
$Qdelete->bind_table(':table_whos_online', TABLE_WHOS_ONLINE);
$Qdelete->bind_value(':time_last_click', $xx_mins_ago);
$Qdelete->execute();
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->size($os_c_template->get_module()) > 0) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
$Qwho = $os_c_database->query('select customer_id, full_name, ip_address, time_entry, time_last_click, session_id from :table_whos_online order by time_last_click desc');
$Qwho->bind_table(':table_whos_online', TABLE_WHOS_ONLINE);
$Qwho->set_batch_limit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
$Qwho->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php 
echo $Qwho->get_batch_total_pages($os_c_language->get('batch_results_number_of_entries'));
?></td>
    <td align="right"><?php 
echo $Qwho->get_batch_page_links('page', $os_c_template->get_module(), false);
?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th width="22">&nbsp;</th>
      <th><?php 
echo $os_c_language->get('table_heading_online');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_customers');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_date_last_click');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_last_page_url');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_shopping_cart_total');
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
      <th align="right" colspan="7"><?php 
echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $os_c_language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />';
?></th>
      <th align="center" width="20"><?php 
echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"');
?></th>
    </tr>
  </tfoot>
  <tbody>

<?php 
while ($Qwho->next()) {
    if (STORE_SESSIONS == 'database') {
        $Qsession = $os_c_database->query('select value from :table_sessions where id = :id');
        $Qsession->bind_table(':table_sessions', TABLE_SESSIONS);
        $Qsession->bind_value(':id', $Qwho->value('session_id'));
        $Qsession->execute();
        $session_data = trim($Qsession->value('value'));
    } else if (file_exists(OSCOM_Registry::get('Session')->get_save_path() . '/sess_' . $Qwho->value('session_id')) && filesize(OSCOM_Registry::get('Session')->get_save_path() . '/sess_' . $Qwho->value('session_id')) > 0) {
        $session_data = trim(file_get_contents(OSCOM_Registry::get('Session')->get_save_path() . '/sess_' . $Qwho->value('session_id')));
    }
    $navigation = unserialize(osc_get_serialized_variable($session_data, 'osC_NavigationHistory_data', 'array'));
    $last_page = end($navigation);
    $currency = unserialize(osc_get_serialized_variable($session_data, 'currency', 'string'));
    $cart = unserialize(osc_get_serialized_variable($session_data, 'osC_ShoppingCart_data', 'array'));
    ?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td align="center">

<?php 
    if ($os_c_geo_ip->is_active() && $os_c_geo_ip->is_valid($Qwho->value('ip_address'))) {
        echo osc_image('../images/worldflags/' . $os_c_geo_ip->get_country_iso_code2($Qwho->value('ip_address')) . '.png', $os_c_geo_ip->get_country_name($Qwho->value('ip_address')) . ', ' . $Qwho->value('ip_address'), 18, 12);
    } else {
        echo osc_image('images/pixel_trans.gif', $Qwho->value('ip_address'), 18, 12);
    }
    ?>

      </td>
      <td><?php 
    echo gmdate('H:i:s', time() - $Qwho->value('time_entry'));
    ?></td>
      <td><?php 
    echo $Qwho->value('full_name') . ' (' . $Qwho->value_int('customer_id') . ')';
    ?></td>
      <td><?php 
    echo date('H:i:s', $Qwho->value('time_last_click'));
    ?></td>
      <td><?php 
    echo $last_page['page'];
    ?></td>
      <td><?php 
    echo $os_c_currencies->format($cart['total_cost'], true, $currency);
    ?></td>
      <td align="right">

<?php 
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&info=' . $Qwho->value('session_id') . '&action=info'), osc_icon('info.png')) . '&nbsp;' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&info=' . $Qwho->value('session_id') . '&action=delete'), osc_icon('trash.png'));
    ?>

      </td>
      <td align="center"><?php 
    echo osc_draw_checkbox_field('batch[]', $Qwho->value('session_id'), null, 'id="batch' . $Qwho->value('session_id') . '"');
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
echo $Qwho->get_batch_pages_pull_down_menu('page', $os_c_template->get_module());
?></td>
  </tr>
</table>

<?php 
if ($os_c_geo_ip->is_active()) {
    $os_c_geo_ip->deactivate();
}