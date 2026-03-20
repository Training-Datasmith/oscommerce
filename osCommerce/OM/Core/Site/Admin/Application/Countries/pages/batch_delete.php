<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
$Qcountries = $OSCOM_PDO->query('select countries_id, countries_name from :table_countries where countries_id in ("' . implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))) . '") order by countries_name');
$Qcountries->execute();
$names_string = '';
while ($Qcountries->fetch()) {
    $names_string .= HTML::hidden_field('batch[]', $Qcountries->value_int('countries_id')) . '<b>' . $Qcountries->value_protected('countries_name') . '</b>, ';
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
echo HTML::icon('trash.png') . ' ' . OSCOM::get_def('action_heading_batch_delete_countries');
?></h3>

  <form name="cDeleteBatch" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'BatchDelete&Process');
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_batch_delete_countries');
?></p>

  <p><?php 
echo $names_string;
?></p>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::get_def('button_delete')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
