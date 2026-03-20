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
use Os_Commerce\OM\Core\Site\Admin\Application\Currencies\Currencies;
use Os_Commerce\OM\Core\Site\Admin\Application\Languages\Languages;
$languages_array = [['id' => '0', 'text' => OSCOM::get_def('none')]];
foreach (Object_Info::to(Languages::get_all(-1))->get('entries') as $l) {
    if ($l['languages_id'] != $_GET['id']) {
        $languages_array[] = ['id' => $l['languages_id'], 'text' => $l['name'] . ' (' . $l['code'] . ')'];
    }
}
$currencies_array = [];
foreach (Object_Info::to(Currencies::get_all(-1))->get('entries') as $c) {
    $currencies_array[] = ['id' => $c['currencies_id'], 'text' => $c['title']];
}
$oscom_object_info = new Object_Info(Languages::get($_GET['id']));
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
echo HTML::icon('edit.png') . ' ' . $oscom_object_info->get_protected('name');
?></h3>

  <form name="lEdit" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'Save&Process&id=' . $_GET['id']);
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_edit_language');
?></p>

  <fieldset>
    <p><label for="name"><?php 
echo OSCOM::get_def('field_name');
?></label><?php 
echo HTML::input_field('name', $oscom_object_info->get('name'));
?></p>
    <p><label for="code"><?php 
echo OSCOM::get_def('field_code');
?></label><?php 
echo HTML::input_field('code', $oscom_object_info->get('code'));
?></p>
    <p><label for="locale"><?php 
echo OSCOM::get_def('field_locale');
?></label><?php 
echo HTML::input_field('locale', $oscom_object_info->get('locale'));
?></p>
    <p><label for="charset"><?php 
echo OSCOM::get_def('field_character_set');
?></label><?php 
echo HTML::input_field('charset', $oscom_object_info->get('charset'));
?></p>
    <p><label for="text_direction"><?php 
echo OSCOM::get_def('field_text_direction');
?></label><?php 
echo HTML::select_menu('text_direction', [['id' => 'ltr', 'text' => 'ltr'], ['id' => 'rtl', 'text' => 'rtl']], $oscom_object_info->get('text_direction'));
?></p>
    <p><label for="date_format_short"><?php 
echo OSCOM::get_def('field_date_format_short');
?></label><?php 
echo HTML::input_field('date_format_short', $oscom_object_info->get('date_format_short'));
?></p>
    <p><label for="date_format_long"><?php 
echo OSCOM::get_def('field_date_format_long');
?></label><?php 
echo HTML::input_field('date_format_long', $oscom_object_info->get('date_format_long'));
?></p>
    <p><label for="time_format"><?php 
echo OSCOM::get_def('field_time_format');
?></label><?php 
echo HTML::input_field('time_format', $oscom_object_info->get('time_format'));
?></p>
    <p><label for="currencies_id"><?php 
echo OSCOM::get_def('field_currency');
?></label><?php 
echo HTML::select_menu('currencies_id', $currencies_array, $oscom_object_info->get('currencies_id'));
?></p>
    <p><label for="numeric_separator_decimal"><?php 
echo OSCOM::get_def('field_currency_separator_decimal');
?></label><?php 
echo HTML::input_field('numeric_separator_decimal', $oscom_object_info->get('numeric_separator_decimal'));
?></p>
    <p><label for="numeric_separator_thousands"><?php 
echo OSCOM::get_def('field_currency_separator_thousands');
?></label><?php 
echo HTML::input_field('numeric_separator_thousands', $oscom_object_info->get('numeric_separator_thousands'));
?></p>
    <p><label for="parent_id"><?php 
echo OSCOM::get_def('field_parent_language');
?></label><?php 
echo HTML::select_menu('parent_id', $languages_array, $oscom_object_info->get('parent_id'));
?></p>
    <p><label for="sort_order"><?php 
echo OSCOM::get_def('field_sort_order');
?></label><?php 
echo HTML::input_field('sort_order', $oscom_object_info->get('sort_order'));
?></p>

<?php 
if ($oscom_object_info->get('code') != DEFAULT_LANGUAGE) {
    ?>

    <p><label for="default"><?php 
    echo OSCOM::get_def('field_set_default');
    ?></label><?php 
    echo HTML::checkbox_field('default');
    ?></p>

<?php 
}
?>

  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
