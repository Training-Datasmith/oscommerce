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
use Os_Commerce\OM\Core\Site\Admin\Application\Countries\Countries;
$oscom_object_info = new Object_Info(Countries::get_zone($_GET['zID']));
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
echo HTML::icon('edit.png') . ' ' . $oscom_object_info->get_protected('zone_name');
?></h3>

  <form name="zEdit" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'ZoneSave&Process&id=' . $_GET['id'] . '&zID=' . $_GET['zID']);
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_edit_zone');
?></p>

  <fieldset>
    <p><label for="zone_name"><?php 
echo OSCOM::get_def('field_zone_name');
?></label><?php 
echo HTML::input_field('zone_name', $oscom_object_info->get('zone_name'));
?></p>
    <p><label for="zone_code"><?php 
echo OSCOM::get_def('field_zone_code');
?></label><?php 
echo HTML::input_field('zone_code', $oscom_object_info->get('zone_code'));
?></p>
  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
