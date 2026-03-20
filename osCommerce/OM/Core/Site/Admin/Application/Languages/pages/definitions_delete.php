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
use Os_Commerce\OM\Core\Site\Admin\Application\Languages\Languages;
$oscom_object_info = new Object_Info(Languages::get_definition($_GET['dID']));
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
echo HTML::icon('trash.png') . ' ' . $oscom_object_info->get_protected('definition_key');
?></h3>

  <form name="lDelete" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'DeleteDefinition&Process&id=' . $_GET['id'] . '&group=' . $_GET['group'] . '&dID=' . $_GET['dID']);
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_delete_language_definition');
?></p>

  <p><?php 
echo '<b>' . $oscom_object_info->get_protected('definition_key') . '</b>';
?></p>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::get_def('button_delete')]) . ' ' . HTML::button(['href' => OSCOM::get_link(null, null, 'id=' . $_GET['id'] . '&group=' . $_GET['group']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
