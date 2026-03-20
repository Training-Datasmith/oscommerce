<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
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
echo HTML::icon('new.png') . ' ' . OSCOM::get_def('action_heading_new_card');
?></h3>

  <form name="ccNew" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'Save&Process');
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_new_card');
?></p>

  <fieldset>
    <p><label for="credit_card_name"><?php 
echo OSCOM::get_def('field_name');
?></label><?php 
echo HTML::input_field('credit_card_name');
?></p>
    <p><label for="pattern"><?php 
echo OSCOM::get_def('field_pattern');
?></label><?php 
echo HTML::input_field('pattern');
?></p>
    <p><label for="sort_order"><?php 
echo OSCOM::get_def('field_sort_order');
?></label><?php 
echo HTML::input_field('sort_order');
?></p>
    <p><label for="credit_card_status"><?php 
echo OSCOM::get_def('field_status');
?></label><?php 
echo HTML::checkbox_field('credit_card_status', '1');
?></p>
  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
