<?php

/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
$os_c_object_info = new Os_C_object_Info(Os_C_product_Types_admin::get_assignments($_GET[$os_c_template->get_module()], $_GET['aID']));
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
echo osc_icon('trash.png') . ' ' . $os_c_object_info->get_protected('action_title');
?></h3>

  <form name="tDelete" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '=' . (int) $_GET[$os_c_template->get_module()] . '&aID=' . $os_c_object_info->get('action') . '&action=entry_delete');
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_delete_assignments');
?></p>

  <p><?php 
echo '<b>' . $os_c_object_info->get_protected('action_title') . '</b>';
?></p>

  <ul>

<?php 
foreach ($os_c_object_info->get('modules') as $module) {
    echo '<li>' . osc_output_string_protected($module['module_title']) . '</li>';
}
?>

  </ul>

  <p><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(['priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::get_def('button_delete')]) . ' ' . osc_draw_button(['href' => osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '=' . $_GET[$os_c_template->get_module()]), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
