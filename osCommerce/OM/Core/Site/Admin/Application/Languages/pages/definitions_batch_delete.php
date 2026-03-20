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
echo HTML::icon('trash.png') . ' ' . HTML::output_protected($_GET['group']);
?></h3>

  <form name="lDeleteBatch" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'BatchDeleteDefinitions&Process&id=' . $_GET['id'] . '&group=' . $_GET['group']);
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_batch_delete_language_definitions');
?></p>

  <fieldset>

<?php 
$names_string = '';
foreach ($_POST['batch'] as $id) {
    $oscom_object_info = new Object_Info(Languages::get_definition($id));
    $names_string .= HTML::hidden_field('batch[]', $oscom_object_info->get_int('id')) . '<b>' . $oscom_object_info->get_protected('definition_key') . '</b><br />';
}
if (!empty($names_string)) {
    $names_string = substr($names_string, 0, -6);
}
echo '<p>' . $names_string . '</p>';
?>

  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::get_def('button_delete')]) . ' ' . HTML::button(['href' => OSCOM::get_link(null, null, 'id=' . $_GET['id'] . '&group=' . $_GET['group']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
