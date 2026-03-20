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
$os_c_directory_listing = new Os_C_directory_Listing('includes/modules/newsletters');
$os_c_directory_listing->set_include_directories(false);
$modules_array = [];
foreach ($os_c_directory_listing->get_files() as $file) {
    $module = substr($file['name'], 0, strrpos($file['name'], '.'));
    $os_c_language->load_ini_file('modules/newsletters/' . $file['name']);
    include 'includes/modules/newsletters/' . $file['name'];
    $newsletter_module_class = 'osC_Newsletter_' . $module;
    $os_c_newsletter_module = new $newsletter_module_class();
    $modules_array[] = ['id' => $module, 'text' => $os_c_newsletter_module->get_title()];
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

<div class="infoBoxHeading"><?php 
echo osc_icon('new.png') . ' ' . $os_c_language->get('action_heading_new_newsletter');
?></div>
<div class="infoBoxContent">
  <form name="newsletter" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&action=save');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_new_newsletter');
?></p>

  <table border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_module') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_pull_down_menu('module', $modules_array);
?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_title') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_input_field('title');
?></td>
    </tr>
    <tr>
      <td width="40%" valign="top"><?php 
echo '<b>' . $os_c_language->get('field_content') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_textarea_field('content', null, 60, 20, 'style="width: 100%;"');
?></td>
    </tr>
  </table>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
