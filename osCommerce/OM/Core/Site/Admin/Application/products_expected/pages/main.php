<?php

/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->exists($os_c_template->get_module())) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
$Qproducts = $os_c_database->query('select p.products_id, pd.products_name, str_to_date(pa.value, "%Y-%m-%d") as products_date_available from :table_products p, :table_products_description pd, :table_product_attributes pa, :table_templates_boxes tb where tb.code = :code and tb.modules_group = :modules_group and tb.id = pa.id and pa.products_id = p.products_id and p.products_id = pd.products_id and pd.language_id = :language_id order by products_date_available');
$Qproducts->bind_table(':table_products', TABLE_PRODUCTS);
$Qproducts->bind_table(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
$Qproducts->bind_table(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
$Qproducts->bind_table(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
$Qproducts->bind_value(':code', 'date_available');
$Qproducts->bind_value(':modules_group', 'product_attributes');
$Qproducts->bind_int(':language_id', $os_c_language->get_id());
$Qproducts->set_batch_limit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
$Qproducts->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php 
echo $Qproducts->get_batch_total_pages($os_c_language->get('batch_results_number_of_entries'));
?></td>
    <td align="right"><?php 
echo $Qproducts->get_batch_page_links('page', $os_c_template->get_module(), false);
?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php 
echo $os_c_language->get('table_heading_products');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_date_expected');
?></th>
      <th width="150"><?php 
echo $os_c_language->get('table_heading_action');
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
while ($Qproducts->next()) {
    ?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td onclick="document.getElementById('batch<?php 
    echo $Qproducts->value_int('products_id');
    ?>').checked = !document.getElementById('batch<?php 
    echo $Qproducts->value_int('products_id');
    ?>').checked;"><?php 
    echo $Qproducts->value('products_name');
    ?></td>
      <td><?php 
    echo Os_C_date_Time::get_short($Qproducts->value('products_date_available'));
    ?></td>
      <td align="right">

<?php 
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&pID=' . $Qproducts->value_int('products_id') . '&action=save'), osc_icon('edit.png'));
    ?>

      </td>
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
echo '<b>' . $os_c_language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $os_c_language->get('icon_edit');
?></td>
    <td align="right"><?php 
echo $Qproducts->get_batch_pages_pull_down_menu('page', $os_c_template->get_module());
?></td>
  </tr>
</table>
