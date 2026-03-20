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
$os_c_currencies = new Os_C_currencies();
$os_c_tax = new Os_C_tax_admin();
$os_c_weight = new Os_C_weight();
$os_c_geo_ip = Os_C_geo_Ip_admin::load();
if ($os_c_geo_ip->is_installed()) {
    $os_c_geo_ip->activate();
}
$os_c_object_info = new Os_C_object_Info(Os_C_whos_Online_admin::get_data($_GET['info']));
if (STORE_SESSIONS == 'database') {
    $Qsession = $os_c_database->query('select value from :table_sessions where id = :id');
    $Qsession->bind_table(':table_sessions', TABLE_SESSIONS);
    $Qsession->bind_value(':id', $os_c_object_info->get('session_id'));
    $Qsession->execute();
    $session_data = trim($Qsession->value('value'));
} else if (file_exists(OSCOM_Registry::get('Session')->get_save_path() . '/sess_' . $os_c_object_info->get('session_id')) && filesize(OSCOM_Registry::get('Session')->get_save_path() . '/sess_' . $os_c_object_info->get('session_id')) > 0) {
    $session_data = trim(file_get_contents(OSCOM_Registry::get('Session')->get_save_path() . '/sess_' . $os_c_object_info->get('session_id')));
}
$navigation = unserialize(osc_get_serialized_variable($session_data, 'osC_NavigationHistory_data', 'array'));
$last_page = end($navigation);
$last_page_url = $last_page['page'];
if (isset($last_page['get']['osCsid'])) {
    unset($last_page['get']['osCsid']);
}
if (sizeof($last_page['get']) > 0) {
    $last_page_url .= '?' . osc_array_to_string($last_page['get']);
}
$currency = unserialize(osc_get_serialized_variable($session_data, 'currency', 'string'));
$cart = unserialize(osc_get_serialized_variable($session_data, 'osC_ShoppingCart_data', 'array'));
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
echo osc_icon('info.png') . ' ' . $os_c_object_info->get('full_name');
?></div>
<div class="infoBoxContent">
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_session_id') . '</b>';
?></td>
      <td width="60%"><?php 
echo $os_c_object_info->get('session_id');
?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_time_online') . '</b>';
?></td>
      <td width="60%"><?php 
echo gmdate('H:i:s', time() - $os_c_object_info->get('time_entry'));
?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_customer_id') . '</b>';
?></td>
      <td width="60%"><?php 
echo $os_c_object_info->get('customer_id');
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_customer_name') . '</b>';
?></td>
      <td width="60%"><?php 
echo $os_c_object_info->get('full_name');
?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_ip_address') . '</b>';
?></td>
      <td width="60%">

<?php 
echo $os_c_object_info->get('ip_address');
if ($os_c_geo_ip->is_active() && $os_c_geo_ip->is_valid($os_c_object_info->get('ip_address'))) {
    echo '<p>' . implode('<br />', $os_c_geo_ip->get_data($os_c_object_info->get('ip_address'))) . '</p>';
}
?>

      </td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_entry_time') . '</b>';
?></td>
      <td width="60%"><?php 
echo date('H:i:s', $os_c_object_info->get('time_entry'));
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_last_click') . '</b>';
?></td>
      <td width="60%"><?php 
echo date('H:i:s', $os_c_object_info->get('time_last_click'));
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_last_page_url') . '</b>';
?></td>
      <td width="60%"><?php 
echo $last_page_url;
?></td>
    </tr>

<?php 
if (!empty($cart['contents'])) {
    echo '    <tr>' . "\n" . '      <td colspan="2">&nbsp;</td>' . "\n" . '    </tr>' . "\n" . '    <tr>' . "\n" . '      <td width="40%" valign="top"><b>' . $os_c_language->get('field_shopping_cart_contents') . '</b></td>' . "\n" . '      <td width="60%"><table border="0" cellspacing="0" cellpadding="2">' . "\n";
    foreach ($cart['contents'] as $product) {
        echo '        <tr>' . "\n" . '          <td align="right">' . $product['quantity'] . ' x</td>' . "\n" . '          <td>' . $product['name'] . '</td>' . "\n" . '        </tr>' . "\n";
    }
    echo '      </table></td>' . "\n" . '    </tr>' . "\n" . '    <tr>' . "\n" . '      <td width="40%"><b>' . $os_c_language->get('field_shopping_cart_total') . '</b></td>' . "\n" . '      <td width="60%">' . $os_c_currencies->format($cart['total_cost'], true, $currency) . '</td>' . "\n" . '    </tr>' . "\n";
}
?>

  </table>

  <p align="center"><?php 
echo '<input type="button" value="' . $os_c_language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
?></p>
</div>

<?php 
if ($os_c_geo_ip->is_active()) {
    $os_c_geo_ip->deactivate();
}