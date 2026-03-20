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
$Qspecial = $os_c_database->query('select p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, s.specials_new_products_price, s.expires_date, s.start_date, s.status from :table_specials s, :table_products p, :table_products_description pd where s.specials_id = :specials_id and s.products_id = p.products_id and p.products_id = pd.products_id and pd.language_id = :language_id');
$Qspecial->bind_table(':table_specials', TABLE_SPECIALS);
$Qspecial->bind_table(':table_products', TABLE_PRODUCTS);
$Qspecial->bind_table(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
$Qspecial->bind_int(':specials_id', $_GET['sID']);
$Qspecial->bind_int(':language_id', $os_c_language->get_id());
$Qspecial->execute();
$Qtc = $os_c_database->query('select tax_class_id, tax_class_title from :table_tax_class order by tax_class_title');
$Qtc->bind_table(':table_tax_class', TABLE_TAX_CLASS);
$Qtc->execute();
$tax_class_array = [];
while ($Qtc->next()) {
    $tax_class_array[] = ['id' => $Qtc->value_int('tax_class_id'), 'text' => $Qtc->value('tax_class_title')];
}
?>

<script type="text/javascript"><!--
  var product_tax = new Array();
  var tax_rates = new Array();

<?php 
echo '  product_tax["' . $Qspecial->value_int('products_id') . '"] = ' . $Qspecial->value_int('products_tax_class_id') . ';' . "\n";
foreach ($tax_class_array as $tc_entry) {
    echo '  tax_rates["' . $tc_entry['id'] . '"] = ' . $os_c_tax->get_tax_rate($tc_entry['id']) . ';' . "\n";
}
?>

  function pad(s) {
    s = s||'.';

    return (s.length>4) ? s : pad(s+'0');
  }

  function doRound(x, places) {
    return (new String(Math.round(x * Math.pow(10, places)) / Math.pow(10, places))).replace(/(\.\d*)?$/, pad);
  }

  function getTaxRate() {
    var products_id = document.forms["special"].products_id.value;
    var tax_class = product_tax[products_id];

    if ( (tax_class > 0) && (tax_rates[tax_class] > 0) ) {
      return tax_rates[tax_class];
    } else {
      return 0;
    }
  }

  function updateGross(field, evt) {
    if (evt.keyCode == 9) {
      return false;
    }

    if ((document.getElementById(field).value).indexOf('%') > -1) {
      document.getElementById(field + "_gross").value = '';
      document.getElementById(field + "_gross").disabled = true;
      return false;
    } else if (document.getElementById(field + "_gross").disabled == true) {
      document.getElementById(field + "_gross").disabled = false;
    }

    var taxRate = getTaxRate();
    var grossValue = document.getElementById(field).value;

    if (taxRate > 0) {
      grossValue = grossValue * ((taxRate / 100) + 1);
    }

    document.getElementById(field + "_gross").value = doRound(grossValue, 4);
  }

  function updateNet(field, evt) {
    if (evt.keyCode == 9) {
      return false;
    }

    if ((document.getElementById(field + "_gross").value).indexOf('%') > -1) {
      document.getElementById(field).value = document.getElementById(field + "_gross").value;
      document.getElementById(field + "_gross").value = '';
      document.getElementById(field).focus();
      document.getElementById(field + "_gross").disabled = true;
      return false;
    }

    var taxRate = getTaxRate();
    var netValue = document.getElementById(field + "_gross").value;

    if (taxRate > 0) {
      netValue = netValue / ((taxRate / 100) + 1);
    }

    document.getElementById(field).value = doRound(netValue, 4);
  }
//--></script>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->size($os_c_template->get_module()) > 0) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<div class="infoBoxHeading"><?php 
echo osc_icon('edit.png') . ' ' . $Qspecial->value('products_name');
?></div>
<div class="infoBoxContent">
  <form name="special" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&sID=' . $_GET['sID'] . '&action=save');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_edit_special');
?></p>

  <p><?php 
echo '<b>' . $Qspecial->value('products_name') . ' (' . $os_c_currencies->format($Qspecial->value_decimal('products_price')) . ')</b>' . osc_draw_hidden_field('products_id', $Qspecial->value_int('products_id'));
?></p>
  <p><?php 
echo '<b>' . $os_c_language->get('field_price_net') . '</b><br />' . osc_draw_input_field('specials_price', $Qspecial->value_decimal('specials_new_products_price'), 'onkeyup="updateGross(\'specials_price\', event)"');
?></p>
  <p><?php 
echo '<b>' . $os_c_language->get('field_price_gross') . '</b><br />' . osc_draw_input_field('specials_price_gross', $Qspecial->value_decimal('specials_new_products_price'), 'onkeyup="updateNet(\'specials_price\', event)"');
?></p>
  <p><?php 
echo '<b>' . $os_c_language->get('field_status') . '</b><br />' . osc_draw_checkbox_field('specials_status', '1', $Qspecial->value('status'));
?></p>
  <p><?php 
echo '<b>' . $os_c_language->get('field_date_start') . '</b><br />' . osc_draw_input_field('specials_start_date', $Qspecial->value('start_date'));
?></p>
  <p><?php 
echo '<b>' . $os_c_language->get('field_date_expires') . '</b><br />' . osc_draw_input_field('specials_expires_date', $Qspecial->value('expires_date'));
?></p>

<script type="text/javascript"><!--
  updateGross('specials_price', false);

  $(function() {
    $("#specials_start_date").datepicker( {
      dateFormat: 'yy-mm-dd',
      changeMonth: true,
      changeYear: true
    } );

    $("#specials_expires_date").datepicker( {
      dateFormat: 'yy-mm-dd',
      changeMonth: true,
      changeYear: true
    } );
  });
//--></script>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
