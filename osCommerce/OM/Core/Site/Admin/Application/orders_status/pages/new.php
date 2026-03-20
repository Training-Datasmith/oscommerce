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
echo osc_icon('new.png') . ' ' . $os_c_language->get('action_heading_new_order_status');
?></div>
<div class="infoBoxContent">
  <form name="osNew" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&action=save');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_new_order_status');
?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_name') . '</b>';
?></td>
      <td width="60%">

<?php 
foreach ($os_c_language->get_all() as $l) {
    echo $os_c_language->show_image($l['code']) . '&nbsp;' . osc_draw_input_field('name[' . $l['id'] . ']') . '<br />';
}
?>

      </td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_set_as_default') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_checkbox_field('default');
?></td>
    </tr>
  </table>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
