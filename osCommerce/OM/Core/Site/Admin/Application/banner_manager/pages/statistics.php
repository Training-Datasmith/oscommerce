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
$type = isset($_GET['type']) ? $_GET['type'] : '';
$Qyears = $os_c_database->query('select distinct year(banners_history_date) as banner_year from :table_banners_history where banners_id = :banners_id');
$Qyears->bind_table(':table_banners_history', TABLE_BANNERS_HISTORY);
$Qyears->bind_int(':banners_id', $_GET['bID']);
$Qyears->execute();
$years_array = [];
while ($Qyears->next()) {
    $years_array[] = ['id' => $Qyears->value_int('banner_year'), 'text' => $Qyears->value_int('banner_year')];
}
$Qyears->free_result();
$months_array = [];
for ($i = 1; $i < 13; $i++) {
    $months_array[] = ['id' => $i, 'text' => strftime('%B', mktime(0, 0, 0, $i))];
}
$type_array = [['id' => 'daily', 'text' => $os_c_language->get('section_daily')], ['id' => 'monthly', 'text' => $os_c_language->get('section_monthly')], ['id' => 'yearly', 'text' => $os_c_language->get('section_yearly')]];
$os_c_object_info = new Os_C_object_Info(Os_C_banner_Manager_admin::get_data($_GET['bID']));
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->size($os_c_template->get_module()) > 0) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<form name="type" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT);
?>" method="get">
  
<?php 
echo osc_draw_hidden_field($os_c_template->get_module()) . osc_draw_hidden_field('page', $_GET['page']) . osc_draw_hidden_field('bID', $_GET['bID']) . osc_draw_hidden_field('action', 'statistics');
?>

<p align="right">

<?php 
echo $os_c_language->get('operation_heading_type') . ' ' . osc_draw_pull_down_menu('type', $type_array, 'daily', 'onchange="this.form.submit();"') . ' ';
switch ($type) {
    case 'yearly':
        break;
    case 'monthly':
        echo $os_c_language->get('operation_heading_year') . ' ' . osc_draw_pull_down_menu('year', $years_array, date('Y'), 'onchange="this.form.submit();"');
        break;
    case 'daily':
    default:
        echo $os_c_language->get('operation_heading_month') . ' ' . osc_draw_pull_down_menu('month', $months_array, date('n'), 'onchange="this.form.submit();"') . ' ' . $os_c_language->get('operation_heading_year') . ' ' . osc_draw_pull_down_menu('year', $years_array, date('Y'), 'onchange="this.form.submit();"');
        break;
}
echo '&nbsp;<input type="button" value="' . $os_c_language->get('button_back') . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&bID=' . $_GET['bID']) . '\';" />';
?>

</p>

</form>

<?php 
if (is_dir('images/graphs') && is_writeable('images/graphs') && !empty($os_c_template->image_extension)) {
    switch ($type) {
        case 'yearly':
            include 'includes/graphs/banner_yearly.php';
            echo '<p align="center">' . osc_image('images/graphs/banner_yearly-' . $_GET['bID'] . '.' . $os_c_template->image_extension) . '</p>';
            break;
        case 'monthly':
            include 'includes/graphs/banner_monthly.php';
            echo '<p align="center">' . osc_image('images/graphs/banner_monthly-' . $_GET['bID'] . '.' . $os_c_template->image_extension) . '</p>';
            break;
        case 'daily':
        default:
            include 'includes/graphs/banner_daily.php';
            echo '<p align="center">' . osc_image('images/graphs/banner_daily-' . $_GET['bID'] . '.' . $os_c_template->image_extension) . '</p>';
            break;
    }
}
?>

<table border="0" width="600" cellspacing="0" cellpadding="2" class="dataTable" align="center">
  <thead>
    <tr>
      <th><?php 
echo $os_c_language->get('table_heading_source');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_views');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_clicks');
?></th>
    </tr>
  </thead>
  <tbody>

<?php 
if (isset($stats)) {
    for ($i = 0, $n = sizeof($stats); $i < $n; $i++) {
        echo '    <tr>' . '      <td>' . $stats[$i][0] . '</td>' . '      <td>' . number_format($stats[$i][1]) . '</td>' . '      <td>' . number_format($stats[$i][2]) . '</td>' . '    </tr>';
    }
}
?>

  </tbody>
</table>
