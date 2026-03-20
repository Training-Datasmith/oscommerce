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
echo HTML::icon('edit.png') . ' ' . OSCOM::get_def('action_heading_batch_edit_cards');
?></h3>

  <form name="ccEditBatch" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'BatchSave&Process');
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_batch_edit_cards');
?></p>

<?php 
$Qcc = $OSCOM_PDO->query('select id, credit_card_name from :table_credit_cards where id in ("' . implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))) . '") order by credit_card_name');
$Qcc->execute();
$names_string = '';
while ($Qcc->fetch()) {
    $names_string .= HTML::hidden_field('batch[]', $Qcc->value_int('id')) . '<b>' . $Qcc->value_protected('credit_card_name') . '</b>, ';
}
if (!empty($names_string)) {
    $names_string = substr($names_string, 0, -2);
}
echo '<p>' . $names_string . '</p>';
echo '<p>' . HTML::radio_field('type', [['id' => 'activate', 'text' => OSCOM::get_def('activate')], ['id' => 'deactivate', 'text' => OSCOM::get_def('deactivate')]], 'activate') . '</p>';
?>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
