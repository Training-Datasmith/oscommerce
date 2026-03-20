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
$Qnewsletters = $os_c_database->query('select newsletters_id, title, length(content) as content_length, module, date_added, date_sent, status, locked from :table_newsletters order by date_added desc');
$Qnewsletters->bind_table(':table_newsletters', TABLE_NEWSLETTERS);
$Qnewsletters->set_batch_limit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
$Qnewsletters->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php 
echo $Qnewsletters->get_batch_total_pages($os_c_language->get('batch_results_number_of_entries'));
?></td>
    <td align="right"><?php 
echo $Qnewsletters->get_batch_page_links('page', $os_c_template->get_module(), false);
?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php 
echo $os_c_language->get('table_heading_newsletters');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_size');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_module');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_sent');
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
echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $os_c_language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />';
?></th>
      <th align="center" width="20"><?php 
echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"');
?></th>
    </tr>
  </tfoot>
  <tbody>

<?php 
while ($Qnewsletters->next()) {
    $newsletter_module_class = 'osC_Newsletter_' . $Qnewsletters->value('module');
    if (!class_exists($newsletter_module_class)) {
        $os_c_language->load_ini_file('modules/newsletters/' . $Qnewsletters->value('module') . '.php');
        include 'includes/modules/newsletters/' . $Qnewsletters->value('module') . '.php';
        ${$newsletter_module_class} = new $newsletter_module_class();
    }
    ?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td><?php 
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&nID=' . $Qnewsletters->value_int('newsletters_id') . '&action=preview'), osc_icon('newsletters.png') . '&nbsp;' . $Qnewsletters->value('title'));
    ?></td>
      <td align="right"><?php 
    echo number_format($Qnewsletters->value_int('content_length'));
    ?></td>
      <td align="right"><?php 
    echo ${$newsletter_module_class}->get_title();
    ?></td>
      <td align="center"><?php 
    echo osc_icon($Qnewsletters->value_int('status') === 1 ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null);
    ?></td>
      <td align="right">

<?php 
    if ($Qnewsletters->value_int('status') === 1) {
        echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&nID=' . $Qnewsletters->value_int('newsletters_id') . '&action=log'), osc_icon('log.png')) . '&nbsp;';
    } else {
        echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&nID=' . $Qnewsletters->value_int('newsletters_id') . '&action=save'), osc_icon('edit.png')) . '&nbsp;' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&nID=' . $Qnewsletters->value_int('newsletters_id') . '&action=send'), osc_icon('email_send.png')) . '&nbsp;';
    }
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&nID=' . $Qnewsletters->value_int('newsletters_id') . '&action=delete'), osc_icon('trash.png'));
    ?>

      </td>
      <td align="center"><?php 
    echo osc_draw_checkbox_field('batch[]', $Qnewsletters->value_int('newsletters_id'), null, 'id="batch' . $Qnewsletters->value_int('newsletters_id') . '"');
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
echo '<b>' . $os_c_language->get('table_action_legend') . '</b> ' . osc_icon('newsletters.png') . '&nbsp;' . $os_c_language->get('icon_newsletters') . '&nbsp;&nbsp;' . osc_icon('edit.png') . '&nbsp;' . $os_c_language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('email_send.png') . '&nbsp;' . $os_c_language->get('icon_email_send') . '&nbsp;&nbsp;' . osc_icon('log.png') . '&nbsp;' . $os_c_language->get('icon_log') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $os_c_language->get('icon_trash');
?></td>
    <td align="right"><?php 
echo $Qnewsletters->get_batch_pages_pull_down_menu('page', $os_c_template->get_module());
?></td>
  </tr>
</table>
