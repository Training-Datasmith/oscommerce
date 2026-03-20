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
use Os_Commerce\OM\Core\Site\Shop\Address;
$oscom_object_info = new Object_Info(Zone_Groups::get_entry($_GET['zID']));
$countries_array = [['id' => '', 'text' => OSCOM::get_def('all_countries')]];
foreach (Address::get_countries() as $country) {
    $countries_array[] = ['id' => $country['id'], 'text' => $country['name']];
}
$zones_array = [['id' => '', 'text' => OSCOM::get_def('all_zones')]];
if ($oscom_object_info->get('zone_country_id') > 0) {
    foreach (Address::get_zones($oscom_object_info->get('zone_country_id')) as $zone) {
        $zones_array[] = ['id' => $zone['id'], 'text' => $zone['name']];
    }
}
?>

<script type="text/javascript">
  function update_zone(theForm) {
    var NumState = theForm.zone_id.options.length;
    var SelectedCountry = "";

    while(NumState > 0) {
      NumState--;
      theForm.zone_id.options[NumState] = null;
    }

    SelectedCountry = theForm.zone_country_id.options[theForm.zone_country_id.selectedIndex].value;

<?php 
echo Zone_Groups::get_js_list('SelectedCountry', 'theForm', 'zone_id');
?>
  }
</script>

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
echo HTML::icon('edit.png') . ' ' . $oscom_object_info->get_protected('countries_name') . ': ' . $oscom_object_info->get_protected('zone_name');
?></h3>

  <form name="zEdit" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'EntrySave&Process&id=' . $_GET['id'] . '&zID=' . $oscom_object_info->get_int('association_id'));
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_edit_zone_entry');
?></p>

  <fieldset>
    <p><label for="zone_country_id"><?php 
echo OSCOM::get_def('field_country');
?></label><?php 
echo HTML::select_menu('zone_country_id', $countries_array, $oscom_object_info->get('zone_country_id'), 'onchange="update_zone(this.form);"');
?></p>
    <p><label for="zone_id"><?php 
echo OSCOM::get_def('field_zone');
?></label><?php 
echo HTML::select_menu('zone_id', $zones_array, $oscom_object_info->get('zone_id'));
?></p>
  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
