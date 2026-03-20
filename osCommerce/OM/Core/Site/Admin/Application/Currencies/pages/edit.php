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
$oscom_object_info = new Object_Info(Currencies::get($_GET['id']));
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

  <form name="cEdit" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'Save&Process&id=' . $_GET['id']);
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_edit_currency');
?></p>

  <fieldset>
    <p><label for="title"><?php 
echo OSCOM::get_def('field_title');
?></label><?php 
echo HTML::input_field('title', $oscom_object_info->get('title'));
?></p>
    <p><label for="code"><?php 
echo OSCOM::get_def('field_code');
?></label><?php 
echo HTML::input_field('code', $oscom_object_info->get('code'));
?></p>
    <p><label for="symbol_left"><?php 
echo OSCOM::get_def('field_symbol_left');
?></label><?php 
echo HTML::input_field('symbol_left', $oscom_object_info->get('symbol_left'));
?></p>
    <p><label for="symbol_right"><?php 
echo OSCOM::get_def('field_symbol_right');
?></label><?php 
echo HTML::input_field('symbol_right', $oscom_object_info->get('symbol_right'));
?></p>
    <p><label for="decimal_places"><?php 
echo OSCOM::get_def('field_decimal_places');
?></label><?php 
echo HTML::input_field('decimal_places', $oscom_object_info->get('decimal_places'));
?></p>
    <p><label for="value"><?php 
echo OSCOM::get_def('field_currency_value');
?></label><?php 
echo HTML::input_field('value', $oscom_object_info->get('value'));
?></p>

<?php 
if ($oscom_object_info->get('code') != DEFAULT_CURRENCY) {
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

  <p>

<?php 
if ($oscom_object_info->get('code') == DEFAULT_CURRENCY) {
    echo HTML::hidden_field('is_default', 'true');
}
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?>

  </p>

  </form>
</div>
