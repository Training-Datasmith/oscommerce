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
echo osc_icon('new.png') . ' ' . $os_c_language->get('action_heading_new_directory');
?></div>
<div class="infoBoxContent">

<?php 
if (is_writeable($_SESSION['fm_directory'])) {
    ?>

  <form name="fmNewDirectory" action="<?php 
    echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&action=saveDirectory');
    ?>" method="post">

  <p><?php 
    echo $os_c_language->get('introduction_new_directory');
    ?></p>

  <p><?php 
    echo '<b>' . osc_output_string_protected($_SESSION['fm_directory']) . '</b>';
    ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php 
    echo '<b>' . $os_c_language->get('field_directory_name') . '</b>';
    ?></td>
      <td width="60%"><?php 
    echo osc_draw_input_field('directory_name', null, 'style="width: 100%;"');
    ?></td>
    </tr>
  </table>

  <p align="center"><?php 
    echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()) . '\';" class="operationButton" />';
    ?></p>

  </form>

<?php 
} else {
    ?>

  <p><?php 
    echo sprintf($os_c_language->get('new_directory_error_not_writable'), $_SESSION['fm_directory']);
    ?></p>

  <p align="center"><?php 
    echo '<input type="button" value="' . $os_c_language->get('button_retry') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&action=saveDirectory') . '\';" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()) . '\';" class="operationButton" />';
    ?></p>

<?php 
}
?>

</div>
