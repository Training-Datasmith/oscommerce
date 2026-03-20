<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Zone_Groups\Zone_Groups;
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
echo HTML::icon('trash.png') . ' ' . OSCOM::get_def('action_heading_batch_delete_zone_groups');
?></h3>

  <form name="zDeleteBatch" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'BatchDelete&Process');
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_batch_delete_zone_groups');
?></p>

<?php 
$check_tax_zones_flag = [];
$Qzones = $OSCOM_PDO->query('select geo_zone_id, geo_zone_name from :table_geo_zones where geo_zone_id in ("' . implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))) . '") order by geo_zone_name');
$Qzones->execute();
$names_string = '';
while ($Qzones->fetch()) {
    if (Zone_Groups::has_tax_rates($Qzones->value_int('geo_zone_id'))) {
        $check_tax_zones_flag[] = $Qzones->value('geo_zone_name');
    }
    $names_string .= HTML::hidden_field('batch[]', $Qzones->value_int('geo_zone_id')) . '<b>' . $Qzones->value_protected('geo_zone_name') . ' (' . sprintf(OSCOM::get_def('total_entries'), Zone_Groups::get_number_of_entries($Qzones->value_int('geo_zone_id'))) . ')</b>, ';
}
if (!empty($names_string)) {
    $names_string = substr($names_string, 0, -2);
}
echo '<p>' . $names_string . '</p>';
if (empty($check_tax_zones_flag)) {
    echo '<p>' . HTML::button(['priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::get_def('button_delete')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]) . '</p>';
} else {
    echo '<p><b>' . OSCOM::get_def('batch_delete_warning_group_in_use_tax_rate') . '</b></p>' . '<p>' . implode(', ', $check_tax_zones_flag) . '</p>';
    echo '<p>' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'primary', 'icon' => 'triangle-1-w', 'title' => OSCOM::get_def('button_back')]) . '</p>';
}
?>

  </form>
</div>
