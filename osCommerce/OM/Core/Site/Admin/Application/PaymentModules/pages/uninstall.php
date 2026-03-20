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
echo HTML::icon('uninstall.png') . ' ' . $oscom_object_info->get_protected('title');
?></h3>

  <form name="mUninstall" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'Uninstall&Process&code=' . $oscom_object_info->get('code'));
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_uninstall_payment_module');
?></p>

  <p><?php 
echo '<b>' . $oscom_object_info->get_protected('title') . '</b>';
?></p>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::get_def('button_uninstall')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
