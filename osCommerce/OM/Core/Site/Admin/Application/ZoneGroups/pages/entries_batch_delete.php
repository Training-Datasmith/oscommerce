<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
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
echo HTML::icon('trash.png') . ' ' . OSCOM::get_def('action_heading_batch_delete_zone_entries');
?></h3>

  <form name="zDeleteBatch" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'BatchDeleteEntries&Process&id=' . $_GET['id']);
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_batch_delete_zone_entries');
?></p>

<?php 
$Qentries = $OSCOM_PDO->query('select z2gz.association_id, z2gz.zone_country_id, c.countries_name, z2gz.zone_id, z.zone_name from :table_zones_to_geo_zones z2gz left join :table_countries c on (z2gz.zone_country_id = c.countries_id) left join :table_zones z on (z2gz.zone_id = z.zone_id) where z2gz.association_id in ("' . implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))) . '") order by c.countries_name, z.zone_name');
$Qentries->execute();
$names_string = '';
while ($Qentries->fetch()) {
    $names_string .= HTML::hidden_field('batch[]', $Qentries->value_int('association_id')) . '<b>' . ($Qentries->value_int('zone_country_id') > 0 ? $Qentries->value('countries_name') : OSCOM::get_def('all_countries')) . ': ' . ($Qentries->value_int('zone_id') > 0 ? $Qentries->value('zone_name') : OSCOM::get_def('all_zones')) . '</b>, ';
}
if (!empty($names_string)) {
    $names_string = substr($names_string, 0, -2) . HTML::hidden_field('subaction', 'confirm');
}
echo '<p>' . $names_string . '</p>';
echo '<p>' . HTML::button(['priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::get_def('button_delete')]) . ' ' . HTML::button(['href' => OSCOM::get_link(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]) . '</p>';
?>

  </form>
</div>
