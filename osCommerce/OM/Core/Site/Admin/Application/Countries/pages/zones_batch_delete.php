<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
$Qzones = $OSCOM_PDO->query('select zone_id, zone_name from :table_zones where zone_id in ("' . implode('", "', array_unique(array_filter($_POST['batch'], 'is_numeric'))) . '") order by zone_name');
$Qzones->execute();
$names_string = '';
while ($Qzones->fetch()) {
    $names_string .= HTML::hidden_field('batch[]', $Qzones->value_int('zone_id')) . '<b>' . $Qzones->value_protected('zone_name') . '</b>, ';
}
if (!empty($names_string)) {
    $names_string = substr($names_string, 0, -2);
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
echo HTML::icon('trash.png') . ' ' . OSCOM::get_def('action_heading_batch_delete_zones');
?></h3>

  <form name="cDeleteBatch" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'BatchDeleteZones&Process&id=' . $_GET['id']);
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_batch_delete_zones');
?></p>

  <p><?php 
echo $names_string;
?></p>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::get_def('button_delete')]) . ' ' . HTML::button(['href' => OSCOM::get_link(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
