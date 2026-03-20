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
echo HTML::icon('edit.png') . ' ' . HTML::output_protected($_GET['group']);
?></h3>

  <form name="lDefineBatch" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'BatchSaveDefinitions&Process&id=' . $_GET['id'] . '&group=' . $_GET['group']);
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_edit_language_definitions');
?></p>

  <fieldset>

<?php 
foreach ($_POST['batch'] as $id) {
    $oscom_object_info = new Object_Info(Languages::get_definition($id));
    echo '<p><label for="def[' . $oscom_object_info->get_protected('definition_key') . ']">' . $oscom_object_info->get_protected('definition_key') . '</label>' . HTML::textarea_field('def[' . $oscom_object_info->get('definition_key') . ']', $oscom_object_info->get('definition_value')) . HTML::hidden_field('batch[]', $oscom_object_info->get_int('id')) . '</p>';
}
?>

  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(null, null, 'id=' . $_GET['id'] . '&group=' . $_GET['group']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
