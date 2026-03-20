<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Configuration\Configuration;
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
echo HTML::icon('edit.png') . ' ' . OSCOM::get_def('action_heading_batch_edit_configuration_parameters');
?></h3>

  <form name="cEditBatch" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'BatchSaveEntries&Process&id=' . $_GET['id']);
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_batch_edit_configuration_parameters');
?></p>

  <fieldset>

<?php 
$Qcfg = $OSCOM_PDO->query('select configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, use_function, set_function from :table_configuration where configuration_id in (' . implode(',', array_unique(array_filter($_POST['batch'], 'is_numeric'))) . ')');
$Qcfg->execute();
while ($Qcfg->fetch()) {
    if (strlen($Qcfg->value('set_function')) > 0) {
        $value_field = Configuration::call_user_func($Qcfg->value('set_function'), $Qcfg->value('configuration_value'), $Qcfg->value('configuration_key'));
    } else {
        $value_field = HTML::input_field('configuration[' . $Qcfg->value('configuration_key') . ']', $Qcfg->value('configuration_value'));
    }
    ?>

    <p><label for="configuration[<?php 
    echo $Qcfg->value_protected('configuration_key');
    ?>]"><?php 
    echo $Qcfg->value_protected('configuration_title');
    ?></label><?php 
    echo $value_field . HTML::hidden_field('batch[]', $Qcfg->value_int('configuration_id'));
    ?></p>

    <p><?php 
    echo $Qcfg->value('configuration_description');
    ?></p>

<?php 
}
?>

  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
