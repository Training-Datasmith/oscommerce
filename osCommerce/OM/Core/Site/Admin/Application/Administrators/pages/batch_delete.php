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
echo HTML::icon('trash.png') . ' ' . OSCOM::get_def('action_heading_batch_delete_administrators');
?></h3>

  <form name="aDeleteBatch" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'BatchDelete&Process');
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_batch_delete_administrators');
?></p>

<?php 
$Qadmins = $OSCOM_PDO->query('select id, user_name from :table_administrators where id in ("' . implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))) . '") order by user_name');
$Qadmins->execute();
$names_string = '';
while ($Qadmins->fetch()) {
    $names_string .= HTML::hidden_field('batch[]', $Qadmins->value_int('id')) . '<b>' . $Qadmins->value_protected('user_name') . '</b>, ';
}
if (!empty($names_string)) {
    $names_string = substr($names_string, 0, -2);
}
echo '<p>' . $names_string . '</p>';
?>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::get_def('button_delete')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
