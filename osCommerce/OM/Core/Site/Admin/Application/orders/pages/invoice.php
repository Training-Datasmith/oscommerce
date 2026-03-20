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
require 'includes/classes/tax.php';
$os_c_tax = new Os_C_tax_admin();
$os_c_order = new Os_C_order($_GET['oID']);
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td class="pageHeading"><?php 
echo nl2br(STORE_NAME_ADDRESS);
?></td>
        <td class="pageHeading" align="right"><?php 
echo osc_image('../images/store_logo.jpg', STORE_NAME);
?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td><b><?php 
echo $os_c_language->get('subsection_billing_address');
?></b></td>
          </tr>
          <tr>
            <td><?php 
echo Os_C_address::format($os_c_order->get_billing(), '<br />');
?></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><?php 
echo $os_c_order->get_customer('telephone');
?></td>
          </tr>
          <tr>
            <td><?php 
echo '<a href="mailto:' . $os_c_order->get_customer('email_address') . '"><u>' . $os_c_order->get_customer('email_address') . '</u></a>';
?></td>
          </tr>
        </table></td>
        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td><b><?php 
echo $os_c_language->get('subsection_shipping_address');
?></b></td>
          </tr>
          <tr>
            <td><?php 
echo Os_C_address::format($os_c_order->get_delivery(), '<br />');
?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td><b><?php 
echo $os_c_language->get('subsection_payment_method');
?></b></td>
        <td><?php 
echo $os_c_order->get_payment_method();
?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
      <thead>
        <tr>
          <th colspan="2"><?php 
echo $os_c_language->get('table_heading_products');
?></th>
          <th><?php 
echo $os_c_language->get('table_heading_product_model');
?></th>
          <th><?php 
echo $os_c_language->get('table_heading_tax');
?></th>
          <th><?php 
echo $os_c_language->get('table_heading_price_net');
?></th>
          <th><?php 
echo $os_c_language->get('table_heading_price_gross');
?></th>
          <th><?php 
echo $os_c_language->get('table_heading_total_net');
?></th>
          <th><?php 
echo $os_c_language->get('table_heading_total_gross');
?></th>
        </tr>
      </thead>
      <tbody>
<?php 
foreach ($os_c_order->get_products() as $product) {
    echo '        <tr>' . "\n" . '          <td valign="top" align="right">' . $product['quantity'] . '&nbsp;x</td>' . "\n" . '          <td valign="top">' . $product['name'];
    if (isset($product['attributes']) && sizeof($product['attributes']) > 0) {
        foreach ($product['attributes'] as $attribute) {
            echo '<br /><nobr>&nbsp;&nbsp;&nbsp;' . $attribute['option'] . ': ' . $attribute['value'] . '</nobr>';
        }
    }
    echo '          </td>' . "\n" . '          <td valign="top">' . $product['model'] . '</td>' . "\n";
    echo '          <td align="right" valign="top">' . $os_c_tax->display_tax_rate_value($product['tax']) . '</td>' . "\n" . '          <td align="right" valign="top"><b>' . $os_c_currencies->format($product['price'], true, $os_c_order->get_currency(), $os_c_order->get_currency_value()) . '</b></td>' . "\n" . '          <td align="right" valign="top"><b>' . $os_c_currencies->display_price_with_tax_rate($product['price'], $product['tax'], 1, true, $os_c_order->get_currency(), $os_c_order->get_currency_value()) . '</b></td>' . "\n" . '          <td align="right" valign="top"><b>' . $os_c_currencies->format($product['price'] * $product['quantity'], true, $os_c_order->get_currency(), $os_c_order->get_currency_value()) . '</b></td>' . "\n" . '          <td align="right" valign="top"><b>' . $os_c_currencies->display_price_with_tax_rate($product['price'], $product['tax'], $product['quantity'], true, $os_c_order->get_currency(), $os_c_order->get_currency_value()) . '</b></td>' . "\n";
    echo '        </tr>' . "\n";
}
?>
      </tbody>
    </table>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php 
foreach ($os_c_order->get_totals() as $total) {
    echo '      <tr>' . "\n" . '        <td align="right">' . $total['title'] . '</td>' . "\n" . '        <td align="right">' . $total['text'] . '</td>' . "\n" . '      </tr>' . "\n";
}
?>
    </table></td>
  </tr>
</table>
