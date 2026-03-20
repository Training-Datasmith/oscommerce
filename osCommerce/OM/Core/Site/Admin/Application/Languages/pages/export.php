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
$oscom_object_info = new Object_Info(Languages::get($_GET['id']));
$groups_array = [];
foreach (Object_Info::to(Languages::get_groups($oscom_object_info->get_int('languages_id')))->get('entries') as $group) {
    $groups_array[] = ['id' => $group['content_group'], 'text' => $group['content_group']];
}
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
echo HTML::icon('export.png') . ' ' . $oscom_object_info->get_protected('name');
?></h3>

  <form name="lExport" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'Export&Process&id=' . $_GET['id']);
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_export_language');
?></p>

  <fieldset>
    <p>(<a href="javascript:selectAllFromPullDownMenu('groups');"><u><?php 
echo OSCOM::get_def('select_all');
?></u></a> | <a href="javascript:resetPullDownMenuSelection('groups');"><u><?php 
echo OSCOM::get_def('select_none');
?></u></a>)<br /><?php 
echo HTML::select_menu('groups[]', $groups_array, ['account', 'checkout', 'general', 'index', 'info', 'order', 'products', 'search'], 'id="groups" size="10" multiple="multiple"');
?></p>

    <p><?php 
echo HTML::checkbox_field('include_data', [['id' => '', 'text' => OSCOM::get_def('field_export_with_data')]], true);
?></p>
  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'triangle-1-nw', 'title' => OSCOM::get_def('button_export')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
