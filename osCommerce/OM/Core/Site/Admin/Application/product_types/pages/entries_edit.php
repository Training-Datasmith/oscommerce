<?php

/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
$os_c_object_info = new Os_C_object_Info(Os_C_product_Types_admin::get_assignments($_GET[$os_c_template->get_module()], $_GET['aID']));
$modules_array = [];
foreach (Os_C_product_Types_admin::get_modules() as $module) {
    $modules_array[$module['id']] = $module['title'];
}
$activated_modules_array = [];
foreach ($os_c_object_info->get('modules') as $module) {
    $activated_modules_array[] = $module['module'];
}
?>

<style type="text/css">
#modulesInstalled, #modulesAvailable {
  list-style-type: none;
  margin-left: 15px;
  padding: 10px 5px;
  width: 60%;
}

#modulesInstalled {
  border: 1px dashed #4F8A10;
  background-color: #DFF2BF;
}

#modulesAvailable {
  border: 1px dashed #D8000C;
  background-color: #FFBABA;
}

#modulesInstalled li, #modulesAvailable li {
  margin: 0 3px 3px 3px;
  padding: 4px;
  padding-left: 20px;
  height: 18px;
  text-align: left;
}
</style>

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
echo osc_icon('edit.png') . ' ' . $os_c_object_info->get_protected('action_title');
?></h3>

  <form name="tEdit" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '=' . (int) $_GET[$os_c_template->get_module()] . '&aID=' . $os_c_object_info->get('action') . '&action=entry_save');
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_edit_assignments');
?></p>

  <fieldset id="containment">
    <p><label>Active Modules:</label><ul id="modulesInstalled" class="connectedList">

<?php 
foreach ($activated_modules_array as $id) {
    echo '<li id="' . $id . '" class="ui-state-default fg-button fg-button-icon-left" onmouseover="$(this).addClass(\'ui-state-highlight\');" onmouseout="$(this).removeClass(\'ui-state-highlight\');"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' . $modules_array[$id] . '</li>';
}
?>

    </ul></p>

    <p><label>Available Modules:</label><ul id="modulesAvailable" class="connectedList">

<?php 
foreach ($modules_array as $id => $title) {
    if (!in_array($id, $activated_modules_array)) {
        echo '<li id="' . $id . '" class="ui-state-default fg-button fg-button-icon-left" onmouseover="$(this).addClass(\'ui-state-highlight\');" onmouseout="$(this).removeClass(\'ui-state-highlight\');"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' . $title . '</li>';
    }
}
?>

    </ul></p>
  </fieldset>

  <p><?php 
echo osc_draw_hidden_field('modules', implode(',', $activated_modules_array), 'id="modules"') . osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . osc_draw_button(['href' => osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '=' . $_GET[$os_c_template->get_module()]), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>

<script type="text/javascript">
  $('#modulesInstalled, #modulesAvailable').sortable({
    containment: '#containment',
    axis: 'y',
    connectWith: '.connectedList',
    update: function(event, ui) {
      $('#modules').val( $('#modulesInstalled').sortable('toArray') );
    }
  }).disableSelection();
</script>
