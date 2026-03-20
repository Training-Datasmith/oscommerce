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
echo HTML::icon('trash.png') . ' ' . $oscom_object_info->get_protected('geo_zone_name');
?></h3>

  <form name="zDelete" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'Delete&Process&id=' . $oscom_object_info->get_int('geo_zone_id'));
?>" method="post">

<?php 
if (Zone_Groups::has_tax_rates($oscom_object_info->get_int('geo_zone_id'))) {
    ?>

  <p><?php 
    echo '<b>' . sprintf(OSCOM::get_def('delete_warning_group_in_use_tax_rate'), Zone_Groups::get_number_of_tax_rates($oscom_object_info->get_int('geo_zone_id'))) . '</b>';
    ?></p>

  <p><?php 
    echo HTML::button(['href' => OSCOM::get_link(), 'icon' => 'triangle-1-w', 'title' => OSCOM::get_def('button_back')]);
    ?></p>

<?php 
} else {
    ?>

  <p><?php 
    echo OSCOM::get_def('introduction_delete_zone_group');
    ?></p>

  <p><?php 
    echo '<b>' . $oscom_object_info->get_protected('geo_zone_name') . ' (' . sprintf(OSCOM::get_def('total_entries'), $oscom_object_info->get_int('total_entries')) . ')</b>';
    ?></p>

  <p><?php 
    echo HTML::button(['priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::get_def('button_delete')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
    ?></p>

<?php 
}
?>

  </form>
</div>
