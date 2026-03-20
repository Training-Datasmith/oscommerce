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
use Os_Commerce\OM\Core\Site\Admin\Application\Services\Services;
$oscom_object_info = new Object_Info(Services::get($_GET['code']));
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
echo HTML::icon('edit.png') . ' ' . $oscom_object_info->get_protected('title');
?></h3>

  <form name="mEdit" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'Save&Process&code=' . $oscom_object_info->get('code'));
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_edit_service_module');
?></p>

<?php 
$keys = '';
foreach ($oscom_object_info->get('keys') as $key) {
    $key_data = OSCOM::call_db('Admin\Configuration\EntryGet', ['key' => $key]);
    $keys .= '<b>' . $key_data['configuration_title'] . '</b><br />' . $key_data['configuration_description'] . '<br />';
    if (strlen($key_data['set_function']) > 0) {
        $keys .= Configuration::call_user_func($key_data['set_function'], $key_data['configuration_value'], $key);
    } else {
        $keys .= HTML::input_field('configuration[' . $key . ']', $key_data['configuration_value']);
    }
    $keys .= '<br /><br />';
}
$keys = substr($keys, 0, strrpos($keys, '<br /><br />'));
?>

  <p><?php 
echo $keys;
?></p>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
