<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Languages\Languages;
$languages_array = [];
foreach (Languages::get_directory_listing() as $directory) {
    $languages_array[] = ['id' => $directory, 'text' => $directory];
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
echo HTML::icon('new.png') . ' ' . OSCOM::get_def('action_heading_import_language');
?></h3>

  <form name="lImport" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'Import&Process');
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_import_language');
?></p>

  <fieldset>
    <p><label for="language_import"><?php 
echo OSCOM::get_def('field_language_selection');
?></label><?php 
echo HTML::select_menu('language_import', $languages_array);
?></p>
    <p><label for="import_type"><?php 
echo OSCOM::get_def('field_import_type');
?></label><br /><?php 
echo HTML::radio_field('import_type', [['id' => 'add', 'text' => OSCOM::get_def('only_add_new_records')], ['id' => 'update', 'text' => OSCOM::get_def('only_update_existing_records')], ['id' => 'replace', 'text' => OSCOM::get_def('replace_all')]], 'add', null, '<br />');
?></p>
  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'triangle-1-se', 'title' => OSCOM::get_def('button_import')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
