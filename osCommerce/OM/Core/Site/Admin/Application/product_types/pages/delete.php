<?php

/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
$os_c_object_info = new Os_C_object_Info(Os_C_product_Types_admin::get($_GET['tID']));
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->exists($os_c_template->get_module())) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<div class="infoBox">
  <h3><?php 
echo osc_icon('trash.png') . ' ' . $os_c_object_info->get_protected('title');
?></h3>

  <form name="tDelete" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&tID=' . $os_c_object_info->get_int('id') . '&action=delete');
?>" method="post">

<?php 
if ($os_c_object_info->get_int('total_products') > 0) {
    ?>

  <p><?php 
    echo '<b>' . sprintf(OSCOM::get_def('delete_error_product_type_in_use'), $os_c_object_info->get_int('total_products')) . '</b>';
    ?></p>

  <p><?php 
    echo osc_draw_button(['href' => osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), 'icon' => 'triangle-1-w', 'title' => OSCOM::get_def('button_back')]);
    ?></p>

<?php 
} else {
    $type_name = $os_c_object_info->get_protected('title');
    if ($os_c_object_info->get_int('total_assignments') > 0) {
        $type_name .= ' (' . sprintf(OSCOM::get_def('total_assignments'), $os_c_object_info->get_int('total_assignments')) . ')';
    }
    ?>

  <p><?php 
    echo OSCOM::get_def('introduction_delete_product_type');
    ?></p>

  <p><?php 
    echo '<b>' . $type_name . '</b>';
    ?></p>

  <p><?php 
    echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(['priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::get_def('button_delete')]) . ' ' . osc_draw_button(['href' => osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
    ?></p>

<?php 
}
?>

  </form>
</div>
