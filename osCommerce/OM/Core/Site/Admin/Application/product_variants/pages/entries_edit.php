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
$os_c_object_info = new Os_C_object_Info(Os_C_product_Variants_admin::get_entry($_GET['paeID']));
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->exists($os_c_template->get_module())) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<div class="infoBoxHeading"><?php 
echo osc_icon('edit.png') . ' ' . $os_c_object_info->get_protected('title');
?></div>
<div class="infoBoxContent">
  <form name="paeEdit" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '=' . $_GET[$os_c_template->get_module()] . '&page=' . $_GET['page'] . '&paeID=' . $os_c_object_info->get_int('id') . '&action=saveEntry');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_edit_group_entry');
?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%" valign="top"><?php 
echo '<b>' . $os_c_language->get('field_group_entry_name') . '</b>';
?></td>
      <td width="60%">

<?php 
$Qed = $os_c_database->query('select languages_id, title from :table_products_variants_values where id = :id');
$Qed->bind_table(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
$Qed->bind_int(':id', $os_c_object_info->get_int('id'));
$Qed->execute();
$entry_names = [];
while ($Qed->next()) {
    $entry_names[$Qed->value_int('languages_id')] = $Qed->value('title');
}
foreach ($os_c_language->get_all() as $l) {
    echo $os_c_language->show_image($l['code']) . '&nbsp;' . osc_draw_input_field('entry_name[' . $l['id'] . ']', isset($entry_names[$l['id']]) ? $entry_names[$l['id']] : null) . '<br />';
}
?>

      </td>
    </tr>
    <tr>
      <td width="40%" valign="top"><?php 
echo '<b>' . $os_c_language->get('field_sort_order') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_input_field('sort_order', $os_c_object_info->get('sort_order'));
?></td>
    </tr>
  </table>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '=' . $_GET[$os_c_template->get_module()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
