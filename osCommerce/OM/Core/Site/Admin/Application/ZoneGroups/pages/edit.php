<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\Object_Info;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Zone_Groups\Zone_Groups;
$oscom_object_info = new Object_Info(Zone_Groups::get($_GET['id']));
?>

<h1><?php 
echo $OSCOM_Template->get_icon(32) . HTML::link(OSCOM::get_link(), $OSCOM_Template->get_page_title());
?></h1>

<?php 
if ($oscom_message_stack->exists()) {
    echo $oscom_message_stack->get();
}
?>

<div class="infoBox">
  <h3><?php 
echo HTML::icon('edit.png') . ' ' . $oscom_object_info->get_protected('geo_zone_name');
?></h3>

  <form name="zEdit" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'Save&Process&id=' . $oscom_object_info->get_int('geo_zone_id'));
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_edit_zone_group');
?></p>

  <fieldset>
    <p><label for="zone_name"><?php 
echo OSCOM::get_def('field_name');
?></label><?php 
echo HTML::input_field('zone_name', $oscom_object_info->get('geo_zone_name'));
?></p>
    <p><label for="zone_description"><?php 
echo OSCOM::get_def('field_description');
?></label><?php 
echo HTML::input_field('zone_description', $oscom_object_info->get('geo_zone_description'));
?></p>
  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
