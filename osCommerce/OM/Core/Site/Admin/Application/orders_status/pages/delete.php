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
$os_c_object_info = new Os_C_object_Info(Os_C_orders_Status_admin::get_data($_GET['osID']));
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
echo osc_icon('trash.png') . ' ' . $os_c_object_info->get('orders_status_name');
?></div>
<div class="infoBoxContent">
  <form name="osDelete" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&osID=' . $os_c_object_info->get('orders_status_id') . '&action=delete');
?>" method="post">

<?php 
/*
  $Qorders = $osC_Database->query('select count(*) as total from :table_orders where orders_status = :orders_status');
  $Qorders->bindTable(':table_orders', TABLE_ORDERS);
  $Qorders->bindInt(':orders_status', $osC_ObjectInfo->get('orders_status_id'));
  $Qorders->execute();

  $Qhistory = $osC_Database->query('select count(*) as total from :table_orders_status_history where orders_status_id = :orders_status_id group by orders_id');
  $Qhistory->bindTable(':table_orders_status_history', TABLE_ORDERS_STATUS_HISTORY);
  $Qhistory->bindInt(':orders_status_id', $osC_ObjectInfo->get('orders_status_id'));
  $Qhistory->execute();

  if ( ( $osC_ObjectInfo->get('orders_status_id') == DEFAULT_ORDERS_STATUS_ID ) || ( $Qorders->valueInt('total') > 0)  || ( $Qhistory->valueInt('total') > 0 ) ) {
    if ( $osC_ObjectInfo->get('orders_status_id') == DEFAULT_ORDERS_STATUS_ID ) {
      echo '  <p><b>' . $osC_Language->get('delete_error_order_status_prohibited') . '</b></p>';
    }

    if ( $Qorders->valueInt('total') > 0 ) {
      echo '  <p><b>' . sprintf($osC_Language->get('delete_error_order_status_in_use'), $Qorders->valueInt('total')) . '</b></p>';
    }

    if ( $Qhistory->valueInt('total') > 0 ) {
      echo '  <p><b>' . sprintf($osC_Language->get('delete_error_order_status_used'), $Qhistory->valueInt('total')) . '</b></p>';
    }

    echo '  <p align="center"><input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
  } else {
*/
?>

  <p><?php 
echo $os_c_language->get('introduction_delete_order_status');
?></p>

  <p><?php 
echo '<b>' . $os_c_object_info->get('orders_status_name') . '</b>';
?></p>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
?></p>

<?php 
//  }
?>

  </form>
</div>
