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
use Os_Commerce\OM\Core\Site\Admin\Application\Tax_Classes\Tax_Classes;
use Os_Commerce\OM\Core\Site\Admin\Application\Zone_Groups\Zone_Groups;
$oscom_object_info = new Object_Info(Tax_Classes::get_entry($_GET['rID']));
$zones_array = [];
foreach (Object_Info::to(Zone_Groups::get_all(-1))->get('entries') as $group) {
    $zones_array[] = ['id' => $group['geo_zone_id'], 'text' => $group['geo_zone_name']];
}
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
echo HTML::icon('edit.png') . ' ' . $oscom_object_info->get_protected('tax_class_title') . ': ' . $oscom_object_info->get_protected('geo_zone_name');
?></h3>

  <form name="rEdit" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'EntrySave&Process&id=' . $_GET['id'] . '&rID=' . $oscom_object_info->get_int('tax_rates_id'));
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_edit_tax_rate');
?></p>

  <fieldset>
    <p><label for="tax_zone_id"><?php 
echo OSCOM::get_def('field_tax_rate_zone_group');
?></label><?php 
echo HTML::select_menu('tax_zone_id', $zones_array, $oscom_object_info->get_int('geo_zone_id'));
?></p>
    <p><label for="tax_rate"><?php 
echo OSCOM::get_def('field_tax_rate');
?></label><?php 
echo HTML::input_field('tax_rate', $oscom_object_info->get('tax_rate'));
?></p>
    <p><label for="tax_description"><?php 
echo OSCOM::get_def('field_tax_rate_description');
?></label><?php 
echo HTML::input_field('tax_description', $oscom_object_info->get('tax_description'));
?></p>
    <p><label for="tax_priority"><?php 
echo OSCOM::get_def('field_tax_rate_priority');
?></label><?php 
echo HTML::input_field('tax_priority', $oscom_object_info->get_int('tax_priority'));
?></p>
  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
