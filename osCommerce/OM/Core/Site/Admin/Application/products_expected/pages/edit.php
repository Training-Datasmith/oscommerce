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
$os_c_object_info = new Os_C_object_Info(Os_C_products_admin::get($_GET['pID']));
$Qdata = $os_c_database->query('select str_to_date(pa.value, "%Y-%m-%d") as products_date_available from :table_product_attributes pa, :table_templates_boxes tb where tb.code = :code and tb.modules_group = :modules_group and tb.id = pa.id');
$Qdata->bind_table(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
$Qdata->bind_table(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
$Qdata->bind_value(':code', 'date_available');
$Qdata->bind_value(':modules_group', 'product_attributes');
$Qdata->execute();
$os_c_object_info->set('products_date_available', $Qdata->value('products_date_available'));
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
echo osc_icon('edit.png') . ' ' . $os_c_object_info->get_protected('products_name');
?></div>
<div class="infoBoxContent">
  <form name="pEdit" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&pID=' . $os_c_object_info->get_int('products_id') . '&action=save');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_edit_product_expected');
?></p>

  <p><?php 
echo $os_c_language->get('field_date_expected') . '<br />' . osc_draw_input_field('products_date_available', $os_c_object_info->get('products_date_available'));
?></p>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>

<script type="text/javascript">
  $(function() {
    $("#products_date_available").datepicker( {
      dateFormat: 'yy-mm-dd',
      changeMonth: true,
      changeYear: true
    } );
  });
</script>
