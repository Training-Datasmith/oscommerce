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
use Os_Commerce\OM\Core\Site\Admin\Application\Configuration\Configuration;
$oscom_object_info = new Object_Info(Configuration::get_entry($_GET['pID']));
?>

<h1><?php 
echo $OSCOM_Template->get_icon(32) . HTML::link(OSCOM::get_link(), $OSCOM_Template->get_page_title());
?></h1>

<?php 
if ($oscom_message_stack->exists()) {
    echo $oscom_message_stack->get();
}
if (strlen($oscom_object_info->get('set_function')) > 0) {
    $value_field = Configuration::call_user_func($oscom_object_info->get('set_function'), $oscom_object_info->get('configuration_value'), $oscom_object_info->get('configuration_key'));
} else {
    $value_field = HTML::input_field('configuration[' . $oscom_object_info->get('configuration_key') . ']', $oscom_object_info->get('configuration_value'));
}
?>

<div class="infoBox">
  <h3><?php 
echo HTML::icon('edit.png') . ' ' . $oscom_object_info->get_protected('configuration_title');
?></h3>

  <form name="cEdit" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'EntrySave&Process&id=' . $_GET['id']);
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_edit_parameter');
?></p>

  <fieldset>
    <p><label for="configuration[<?php 
echo $oscom_object_info->get('configuration_key');
?>]"><?php 
echo $oscom_object_info->get_protected('configuration_title');
?></label><?php 
echo $value_field;
?></p>
    <p><?php 
echo $oscom_object_info->get('configuration_description');
?></p>
  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
