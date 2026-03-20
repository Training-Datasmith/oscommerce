<?php

/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
$os_c_object_info = new Os_C_object_Info(Os_C_administrators_Log_admin::get_data($_GET['lID']));
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
echo '<input type="button" value="' . $os_c_language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu']) . '\';" class="operationButton" />';
?></p>

<div class="infoBoxHeading"><?php 
echo osc_icon('info.png') . ' ' . $os_c_object_info->get('user_name') . ' &raquo; ' . $os_c_object_info->get('module_action') . ' &raquo; ' . $os_c_object_info->get('module') . ' &raquo; ' . $os_c_object_info->get('module_id');
?></div>
<div class="infoBoxContent">
  <p><?php 
echo '<b>' . $os_c_language->get('field_date') . '</b> ' . date('d M Y H:i:s', $os_c_object_info->get('datestamp'));
?></p>
</div>

<br />

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php 
echo $os_c_language->get('table_heading_fields');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_value_old');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_value_new');
?></th>
    </tr>
  </thead>
  <tbody>

<?php 
$Qentries = $os_c_database->query('select action, field_key, old_value, new_value from :table_administrators_log where id = :id');
$Qentries->bind_table(':table_administrators_log', TABLE_ADMINISTRATORS_LOG);
$Qentries->bind_int(':id', $os_c_object_info->get('id'));
$Qentries->execute();
while ($Qentries->next()) {
    switch ($Qentries->value('action')) {
        case 'delete':
            $bg_color = '#E23832';
            break;
        case 'insert':
            $bg_color = '#96E97A';
            break;
        default:
            $bg_color = '#FFC881';
            break;
    }
    ?>

    <tr>
      <td valign="top" style="background-color: <?php 
    echo $bg_color;
    ?>;"><?php 
    echo $Qentries->value_protected('field_key');
    ?></td>
      <td valign="top" style="background-color: <?php 
    echo $bg_color;
    ?>;"><?php 
    echo nl2br($Qentries->value_protected('old_value'));
    ?></td>
      <td valign="top" style="background-color: <?php 
    echo $bg_color;
    ?>;"><?php 
    echo nl2br($Qentries->value_protected('new_value'));
    ?></td>
    </tr>

<?php 
}
?>

  </tbody>
</table>
