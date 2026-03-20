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
$Qp = $os_c_database->query('select p.products_id, p.products_quantity, p.products_price, p.products_model, p.products_weight, p.products_weight_class, p.products_date_added, p.products_last_modified, p.products_status, p.products_tax_class_id, p.manufacturers_id, i.image from :table_products p left join :table_products_images i on (p.products_id = i.products_id and default_flag = :default_flag) where p.products_id = :products_id');
$Qp->bind_table(':table_products', TABLE_PRODUCTS);
$Qp->bind_table(':table_products_images', TABLE_PRODUCTS_IMAGES);
$Qp->bind_int(':products_id', $_GET[$os_c_template->get_module()]);
$Qp->bind_int(':default_flag', 1);
$Qp->execute();
$Qpd = $os_c_database->query('select products_name, products_description, products_url, language_id from :table_products_description where products_id = :products_id');
$Qpd->bind_table(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
$Qpd->bind_int(':products_id', $_GET[$os_c_template->get_module()]);
$Qpd->execute();
$pd_extra = [];
while ($Qpd->next()) {
    $pd_extra['products_name'][$Qpd->value_int('language_id')] = $Qpd->value_protected('products_name');
    $pd_extra['products_description'][$Qpd->value_int('language_id')] = $Qpd->value('products_description');
    $pd_extra['products_url'][$Qpd->value_int('language_id')] = $Qpd->value_protected('products_url');
}
$os_c_object_info = new Os_C_object_Info(array_merge($Qp->to_array(), $pd_extra));
$products_name = $os_c_object_info->get('products_name');
$products_description = $os_c_object_info->get('products_description');
$products_url = $os_c_object_info->get('products_url');
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->exists($os_c_template->get_module())) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<div style="background-color: #fff3e7;">

<?php 
foreach ($os_c_language->get_all() as $l) {
    echo '<span id="lang_' . $l['code'] . '"' . ($l['code'] == $os_c_language->get_code() ? ' class="highlight"' : '') . '><a href="javascript:toggleDivBlocks(\'pName_\', \'pName_' . $l['code'] . '\'); toggleClass(\'lang_\', \'lang_' . $l['code'] . '\', \'highlight\', \'span\');">' . $os_c_language->show_image($l['code']) . '</a></span>&nbsp;&nbsp;';
}
?>

</div>

<?php 
foreach ($os_c_language->get_all() as $l) {
    ?>

<div id="pName_<?php 
    echo $l['code'];
    ?>" <?php 
    echo $l['code'] != $os_c_language->get_code() ? ' style="display: none;"' : '';
    ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td><h1><?php 
    echo osc_output_string_protected($products_name[$l['id']]) . (!osc_empty($os_c_object_info->get('products_model')) ? '<br /><span>' . $os_c_object_info->get_protected('products_model') . '</span>' : '');
    ?></h1></td>
      <td align="right"><h1><?php 
    echo $os_c_currencies->format($os_c_object_info->get('products_price'));
    ?></h1></td>
    </tr>
  </table>

  <p><?php 
    echo $os_c_image->show($os_c_object_info->get('image'), $products_name[$l['id']], 'align="right" hspace="5" vspace="5"', 'product_info') . $products_description[$l['id']];
    ?></p>

<?php 
    if (!empty($products_url[$l['id']])) {
        echo '<p>' . sprintf($os_c_language->get('more_product_information'), osc_output_string_protected($products_url[$l['id']])) . '</p>';
    }
    ?>

<?php 
    // HPDL
    //    if ($osC_ObjectInfo->get('products_date_available') > date('Y-m-d')) {
    //      echo '<p align="center">' . sprintf($osC_Language->get('product_date_available'), osC_DateTime::getLong($osC_ObjectInfo->get('products_date_available'))) . '</p>';
    //    } else {
    echo '<p align="center">' . sprintf($os_c_language->get('product_date_added'), Os_C_date_Time::get_long($os_c_object_info->get('products_date_added'))) . '</p>';
    //    }
    ?>

</div>

<?php 
}
?>

<p align="right"><?php 
echo '<input type="button" value="' . $os_c_language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&cID=' . $_GET['cID']) . '\';" class="operationButton" />';
?></p>
