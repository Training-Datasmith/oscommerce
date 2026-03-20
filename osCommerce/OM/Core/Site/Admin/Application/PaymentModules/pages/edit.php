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
use Os_Commerce\OM\Core\Site\Admin\Application\Payment_Modules\Payment_Modules;
$oscom_object_info = new Object_Info(Payment_Modules::get($_GET['code']));
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

  <form name="pmEdit" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'Save&Process&code=' . $oscom_object_info->get('code'));
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_edit_payment_module');
?></p>

  <fieldset>

<?php 
$keys = '';
foreach ($oscom_object_info->get('keys') as $key) {
    $Qkey = $OSCOM_PDO->prepare('select configuration_title, configuration_value, configuration_description, use_function, set_function from :table_configuration where configuration_key = :configuration_key');
    $Qkey->bind_value(':configuration_key', $key);
    $Qkey->execute();
    $keys .= '<p><label for="' . $key . '">' . $Qkey->value('configuration_title') . '</label><br />' . $Qkey->value('configuration_description');
    if (strlen($Qkey->value('set_function')) > 0) {
        $keys .= Configuration::call_user_func($Qkey->value('set_function'), $Qkey->value('configuration_value'), $key);
    } else {
        $keys .= HTML::input_field('configuration[' . $key . ']', $Qkey->value('configuration_value'));
    }
    $keys .= '</p>';
}
echo $keys;
?>

  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
