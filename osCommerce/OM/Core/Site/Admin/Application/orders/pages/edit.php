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
if (!$os_c_order->is_valid()) {
    $os_c_message_stack->add($os_c_template->get_module(), sprintf(ERROR_ORDER_DOES_NOT_EXIST, $_GET['oID']), 'error');
}
$tab_index = 0;
if (isset($_GET['tabIndex']) && !empty($_GET['tabIndex'])) {
    switch ($_GET['tabIndex']) {
        case 'tabProducts':
            $tab_index = 1;
            break;
        case 'tabTransactionHistory':
            $tab_index = 2;
            break;
        case 'tabStatusHistory':
            $tab_index = 3;
            break;
    }
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

<p align="right">
  <?php 
echo '<input type="button" value="' . $os_c_language->get('button_orders_invoice') . '" onclick="window.open(\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&oID=' . $_GET['oID'] . '&action=invoice') . '\');" class="infoBoxButton"/> <input type="button" value="' . $os_c_language->get('button_orders_packaging_slip') . '" onclick="window.open(\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&oID=' . $_GET['oID'] . '&action=packaging_slip') . '\');" class="infoBoxButton" /> <input type="button" value="' . $os_c_language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page']) . '\';" class="operationButton" />';
?>
</p>

<?php 
if ($os_c_order->is_valid()) {
    ?>

<script type="text/javascript">
  var tabIndex = <?php 
    echo (int) $tab_index;
    ?>;

  $(document).ready(function(){
    $("#orderTabs").tabs( { selected: tabIndex } );
  });
</script>

<div id="orderTabs">
  <ul>
    <li><?php 
    echo osc_link_object('#section_summary_content', $os_c_language->get('section_summary'));
    ?></li>
    <li><?php 
    echo osc_link_object('#section_products_content', $os_c_language->get('section_products'));
    ?></li>
    <li><?php 
    echo osc_link_object('#section_transaction_history_content', $os_c_language->get('section_transaction_history'));
    ?></li>
    <li><?php 
    echo osc_link_object('#section_status_history_content', $os_c_language->get('section_status_history'));
    ?></li>
  </ul>

  <div id="section_summary_content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php 
    echo osc_icon('personal.png') . ' ' . $os_c_language->get('subsection_customer');
    ?></legend>

            <p><?php 
    echo Os_C_address::format($os_c_order->get_customer(), '<br />');
    ?></p>
            <p><?php 
    echo osc_icon('telephone.png') . ' ' . $os_c_order->get_customer('telephone') . '<br />' . osc_icon('write.png') . ' ' . $os_c_order->get_customer('email_address');
    ?></p>
          </fieldset>
        </td>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php 
    echo osc_icon('home.png') . ' ' . $os_c_language->get('subsection_shipping_address');
    ?></legend>

            <p><?php 
    echo Os_C_address::format($os_c_order->get_delivery(), '<br />');
    ?></p>
          </fieldset>
        </td>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php 
    echo osc_icon('bill.png') . ' ' . $os_c_language->get('subsection_billing_address');
    ?></legend>

            <p><?php 
    echo Os_C_address::format($os_c_order->get_billing(), '<br />');
    ?></p>
          </fieldset>
        </td>
      </tr>
      <tr>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php 
    echo osc_icon('payment.png') . ' ' . $os_c_language->get('subsection_payment_method');
    ?></legend>

            <p><?php 
    echo $os_c_order->get_payment_method();
    ?></p>

<?php 
    if ($os_c_order->is_valid_credit_card()) {
        ?>

            <table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><?php 
        echo $os_c_language->get('credit_card_type');
        ?></td>
                <td><?php 
        echo $os_c_order->get_credit_card_details('type');
        ?></td>
              </tr>
              <tr>
                <td><?php 
        echo $os_c_language->get('credit_card_owner_name');
        ?></td>
                <td><?php 
        echo $os_c_order->get_credit_card_details('owner');
        ?></td>
              </tr>
              <tr>
                <td><?php 
        echo $os_c_language->get('credit_card_number');
        ?></td>
                <td><?php 
        echo $os_c_order->get_credit_card_details('number');
        ?></td>
              </tr>
              <tr>
                <td><?php 
        echo $os_c_language->get('credit_card_expiry_date');
        ?></td>
                <td><?php 
        echo $os_c_order->get_credit_card_details('expires');
        ?></td>
              </tr>
            </table>

<?php 
    }
    ?>
          </fieldset>
        </td>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php 
    echo osc_icon('history.png') . ' ' . $os_c_language->get('subsection_status');
    ?></legend>

            <p><?php 
    echo $os_c_order->get_status() . '<br />' . ($os_c_order->get_date_last_modified() > $os_c_order->get_date_created() ? Os_C_date_Time::get_short($os_c_order->get_date_last_modified(), true) : Os_C_date_Time::get_short($os_c_order->get_date_created(), true));
    ?></p>
            <p><?php 
    echo $os_c_language->get('number_of_comments') . ' ' . $os_c_order->get_number_of_comments();
    ?></p>
          </fieldset>
        </td>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php 
    echo osc_icon('calculator.png') . ' ' . $os_c_language->get('subsection_total');
    ?></legend>

            <p><?php 
    echo $os_c_order->get_total();
    ?></p>
            <p><?php 
    echo $os_c_language->get('number_of_products') . ' ' . $os_c_order->get_number_of_products() . '<br />' . $os_c_language->get('number_of_items') . ' ' . $os_c_order->get_number_of_items();
    ?></p>
          </fieldset>
        </td>
      </tr>
    </table>
  </div>

  <div id="section_products_content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
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
    foreach ($os_c_order->get_products() as $products) {
        ?>

        <tr>
          <td valign="top" align="right"><?php 
        echo $products['quantity'] . '&nbsp;x';
        ?></td>
          <td valign="top">

<?php 
        echo $products['name'];
        if (isset($products['attributes']) && is_array($products['attributes']) && sizeof($products['attributes']) > 0) {
            foreach ($products['attributes'] as $attributes) {
                echo '<br /><nobr>&nbsp;&nbsp;&nbsp;<i>' . $attributes['option'] . ': ' . $attributes['value'] . '</i></nobr>';
            }
        }
        ?>

          </td>
          <td valign="top"><?php 
        echo $products['model'];
        ?></td>
          <td valign="top" align="right"><?php 
        echo $os_c_tax->display_tax_rate_value($products['tax']);
        ?></td>
          <td valign="top" align="right"><?php 
        echo $os_c_currencies->format($products['price'], $os_c_order->get_currency(), $os_c_order->get_currency_value());
        ?></td>
          <td valign="top" align="right"><?php 
        echo $os_c_currencies->display_price_with_tax_rate($products['price'], $products['tax'], 1, true, $os_c_order->get_currency(), $os_c_order->get_currency_value());
        ?></td>
          <td valign="top" align="right"><?php 
        echo $os_c_currencies->format($products['price'] * $products['quantity'], $os_c_order->get_currency(), $os_c_order->get_currency_value());
        ?></td>
          <td valign="top" align="right"><?php 
        echo $os_c_currencies->display_price_with_tax_rate($products['price'], $products['tax'], $products['quantity'], true, $os_c_order->get_currency(), $os_c_order->get_currency_value());
        ?></td>
        </tr>

<?php 
    }
    ?>

      </tbody>
    </table>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tbody>

<?php 
    foreach ($os_c_order->get_totals() as $totals) {
        ?>

        <tr>
          <td align="right"><?php 
        echo $totals['title'];
        ?></td>
          <td align="right"><?php 
        echo $totals['text'];
        ?></td>
        </tr>

<?php 
    }
    ?>

      </tbody>
    </table>
  </div>

  <div id="section_transaction_history_content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
      <thead>
        <tr>
          <th width="130"><?php 
    echo $os_c_language->get('table_heading_date_added');
    ?></th>
          <th width="50"><?php 
    echo $os_c_language->get('table_heading_status');
    ?></th>
          <th width="20">&nbsp;</th>
          <th><?php 
    echo $os_c_language->get('table_heading_comments');
    ?></th>
        </tr>
      </thead>
      <tbody>

<?php 
    foreach ($os_c_order->get_transaction_history() as $history) {
        ?>

        <tr>
          <td valign="top"><?php 
        echo Os_C_date_Time::get_short($history['date_added'], true);
        ?></td>
          <td valign="top"><?php 
        echo !empty($history['status']) ? $history['status'] : $history['status_id'];
        ?></td>
          <td valign="top" align="center"><?php 
        echo osc_icon($history['return_status'] === 1 ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null);
        ?></td>
          <td valign="top"><?php 
        echo nl2br($history['return_value']);
        ?></td>
        </tr>

<?php 
    }
    ?>

      </tbody>
    </table>

<?php 
    if ($os_c_order->has_post_transaction_actions()) {
        ?>

    <br />

    <form name="transaction" action="<?php 
        echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $_GET['oID'] . '&action=updateTransaction');
        ?>" method="post">

    <p><?php 
        echo $os_c_language->get('field_post_transaction_actions') . ' ' . osc_draw_pull_down_menu('transaction', $os_c_order->get_post_transaction_actions()) . ' ' . osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_execute') . '" class="operationButton" />';
        ?></p>

    </form>

<?php 
    }
    ?>

  </div>

  <div id="section_status_history_content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
      <thead>
        <tr>
          <th><?php 
    echo $os_c_language->get('table_heading_date_added');
    ?></th>
          <th><?php 
    echo $os_c_language->get('table_heading_status');
    ?></th>
          <th><?php 
    echo $os_c_language->get('table_heading_comments');
    ?></th>
          <th align="right"><?php 
    echo $os_c_language->get('table_heading_customer_notified');
    ?></th>
        </tr>
      </thead>
      <tbody>

<?php 
    foreach ($os_c_order->get_status_history() as $status_history) {
        ?>

        <tr>
          <td valign="top"><?php 
        echo Os_C_date_Time::get_short($status_history['date_added'], true);
        ?></td>
          <td valign="top"><?php 
        echo $status_history['status'];
        ?></td>
          <td valign="top"><?php 
        echo nl2br($status_history['comment']);
        ?></td>
          <td align="right" valign="top"><?php 
        echo osc_icon($status_history['customer_notified'] === 1 ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null);
        ?></td>
        </tr>

<?php 
    }
    ?>
      </tbody>
    </table>

    <br />

    <form name="status" action="<?php 
    echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $_GET['oID'] . '&action=updateStatus');
    ?>" method="post">

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><?php 
    echo $os_c_language->get('field_status');
    ?></td>
        <td><?php 
    echo osc_draw_pull_down_menu('status', $orders_statuses, $os_c_order->get_status_id());
    ?></td>
      </tr>
      <tr>
        <td valign="top"><?php 
    echo $os_c_language->get('field_add_comment');
    ?></td>
        <td><?php 
    echo osc_draw_textarea_field('comment', null, null, null, 'style="width: 100%"');
    ?></td>
      </tr>
      <tr>
        <td><?php 
    echo $os_c_language->get('field_notify_customer');
    ?></td>
        <td><?php 
    echo osc_draw_checkbox_field('notify_customer', null, true);
    ?></td>
      </tr>
        <td><?php 
    echo $os_c_language->get('field_notify_customer_with_comments');
    ?></td>
        <td><?php 
    echo osc_draw_checkbox_field('append_comment', null, true);
    ?></td>
      </tr>
      <tr>
        <td colspan="2" align="right"><?php 
    echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_update') . '" class="operationButton" />';
    ?></td>
      </tr>
    </table>

    </form>
  </div>
</div>

<?php 
}