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
if (!isset($_GET['lpage']) || isset($_GET['lpage']) && !is_numeric($_GET['lpage'])) {
    $_GET['lpage'] = 1;
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

<p align="right"><?php 
echo '<input type="button" value="' . $os_c_language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
?></p>

<?php 
$Qlog = $os_c_database->query('select email_address, date_sent from :table_newsletters_log where newsletters_id = :newsletters_id order by date_sent desc');
$Qlog->bind_table(':table_newsletters_log', TABLE_NEWSLETTERS_LOG);
$Qlog->bind_int(':newsletters_id', $_GET['nID']);
$Qlog->set_batch_limit($_GET['lpage'], MAX_DISPLAY_SEARCH_RESULTS);
$Qlog->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php 
echo $Qlog->get_batch_total_pages($os_c_language->get('batch_results_number_of_entries'));
?></td>
    <td align="right"><?php 
echo $Qlog->get_batch_page_links('lpage', $os_c_template->get_module() . '&page=' . $_GET['page'] . '&nID=' . $_GET['nID'], false);
?></td>
  </tr>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php 
echo $os_c_language->get('table_heading_email_addresses');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_sent');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_date_sent');
?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="3">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>

<?php 
while ($Qlog->next()) {
    ?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td><?php 
    echo $Qlog->value_protected('email_address');
    ?></td>
      <td align="center"><?php 
    echo osc_icon(!osc_empty($Qlog->value('date_sent')) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null);
    ?></td>
      <td align="right"><?php 
    echo $Qlog->value('date_sent');
    ?></td>
    </tr>

<?php 
}
?>

  </tbody>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td align="right"><?php 
echo $Qlog->get_batch_pages_pull_down_menu('lpage', $os_c_template->get_module() . '&page=' . $_GET['page'] . '&nID=' . $_GET['nID']);
?></td>
  </tr>
</table>
