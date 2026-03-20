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
$writeable = true;
$contents = '';
$target = $_SESSION['fm_directory'] . '/' . basename($_GET['entry']);
if (!is_writeable($target)) {
    $writeable = false;
    $os_c_message_stack->add($os_c_template->get_module(), sprintf($os_c_language->get('ms_error_file_not_writable'), $target), 'warning');
}
$contents = file_get_contents($target);
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
echo osc_icon('edit.png') . ' ' . $os_c_language->get('action_heading_edit_file');
?></div>
<div class="infoBoxContent">
  <form name="file_manager" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&action=save');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_edit_file');
?></p>

  <p><?php 
echo '<b>' . osc_output_string_protected($_SESSION['fm_directory']) . '</b>';
?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_file_name') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_output_string_protected($_GET['entry']) . osc_draw_hidden_field('filename', $_GET['entry']);
?></td>
    </tr>
    <tr>
      <td width="40%" valign="top"><?php 
echo '<b>' . $os_c_language->get('field_file_contents') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_textarea_field('contents', $contents, 80, 20, 'style="width: 100%;"' . ($writeable === true ? '' : ' readonly="readonly"'));
?></td>
    </tr>
  </table>

  <p align="center">

<?php 
if ($writeable === true) {
    echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()) . '\';" class="operationButton" />';
} else {
    echo '<input type="button" value="' . $os_c_language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()) . '\';" class="operationButton" />';
}
?>

  </p>

  </form>
</div>
