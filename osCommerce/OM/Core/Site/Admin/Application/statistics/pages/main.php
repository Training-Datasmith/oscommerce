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
include 'includes/modules/statistics/' . $_GET['module'] . '.php';
$class = 'osC_Statistics_' . str_replace(' ', '_', ucwords(str_replace('_', ' ', $_GET['module'])));
$os_c_statistics = new $class();
$os_c_statistics->activate();
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title() . ': ' . $os_c_statistics->get_title());
?></h1>

<?php 
if ($os_c_message_stack->size($os_c_template->get_module()) > 0) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<p align="right"><?php 
echo '<input type="button" value="' . $os_c_language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()) . '\';" class="operationButton">';
?></p>

<?php 
if ($os_c_statistics->is_batch_query()) {
    ?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php 
    echo $os_c_statistics->get_batch_total_pages($os_c_language->get('batch_results_number_of_entries'));
    ?></td>
    <td align="right"><?php 
    echo $os_c_statistics->get_batch_page_links('page', $os_c_template->get_module() . '&module=' . $_GET['module'], false);
    ?></td>
  </tr>
</table>

<?php 
}
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>

<?php 
foreach ($os_c_statistics->get_header() as $header) {
    echo '      <th>' . $header . '</th>' . "\n";
}
?>

    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="<?php 
echo sizeof($os_c_statistics->get_header());
?>">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>

<?php 
foreach ($os_c_statistics->get_data() as $data) {
    if (!isset($columns)) {
        $columns = sizeof($data);
    }
    ?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">

<?php 
    for ($i = 0; $i < $columns; $i++) {
        echo '      <td>' . $data[$i] . '</td>' . "\n";
    }
    ?>

    </tr>

<?php 
}
?>

  </tbody>
</table>

<?php 
if ($os_c_statistics->is_batch_query()) {
    ?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td align="right"><?php 
    echo $os_c_statistics->get_batch_pages_pull_down_menu('page', $os_c_template->get_module() . '&module=', $_GET['module']);
    ?></td>
  </tr>
</table>

<?php 
}