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
echo osc_icon('edit.png') . ' ' . $os_c_object_info->get_protected('title');
?></h3>

  <form name="tEdit" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&tID=' . $os_c_object_info->get_int('id') . '&action=save');
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_edit_product_type');
?></p>

  <fieldset>
    <p><label for="title"><?php 
echo OSCOM::get_def('field_title');
?></label><?php 
echo osc_draw_input_field('title', $os_c_object_info->get('title'));
?></p>
  </fieldset>

  <p><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . osc_draw_button(['href' => osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
