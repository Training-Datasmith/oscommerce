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
$Qgroups = $os_c_database->query('select distinct banners_group from :table_banners order by banners_group');
$Qgroups->bind_table(':table_banners', TABLE_BANNERS);
$Qgroups->execute();
$groups_array = [];
while ($Qgroups->next()) {
    $groups_array[] = ['id' => $Qgroups->value('banners_group'), 'text' => $Qgroups->value('banners_group')];
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
echo osc_icon('new.png') . ' ' . $os_c_language->get('action_heading_new_banner');
?></div>
<div class="infoBoxContent">
  <form name="bNew" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&action=save');
?>" method="post" enctype="multipart/form-data">

  <p><?php 
echo $os_c_language->get('introduction_new_banner');
?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_title') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_input_field('title', null, 'style="width: 100%;"');
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_url') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_input_field('url', null, 'style="width: 100%;"');
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_group') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_pull_down_menu('group', $groups_array) . $os_c_language->get('field_group_new') . '<br />' . osc_draw_input_field('group_new', null, 'style="width: 100%;"');
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_image') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_file_field('image', true) . ' ' . $os_c_language->get('field_image_local') . '<br />' . realpath('../images/') . '/' . osc_draw_input_field('image_local');
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_image_target') . '</b>';
?></td>
      <td width="60%"><?php 
echo realpath('../images') . '/' . osc_draw_input_field('image_target');
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_html_text') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_textarea_field('html_text');
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_scheduled_date') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_input_field('date_scheduled');
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_expiry_date') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_input_field('date_expires');
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_maximum_impressions') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_input_field('expires_impressions', null, 'maxlength="7" size="7"');
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_status') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_checkbox_field('status');
?></td>
    </tr>
  </table>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>

<script type="text/javascript"><!--
  $(function() {
    $("#date_scheduled").datepicker( {
      dateFormat: 'yy-mm-dd',
      changeMonth: true,
      changeYear: true
    } );

    $("#date_expires").datepicker( {
      dateFormat: 'yy-mm-dd',
      changeMonth: true,
      changeYear: true
    } );
  });
//--></script>

<p><?php 
echo $os_c_language->get('info_banner_fields');
?></p>
