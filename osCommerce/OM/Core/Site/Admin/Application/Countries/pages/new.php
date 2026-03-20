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
echo HTML::icon('new.png') . ' ' . OSCOM::get_def('action_heading_new_country');
?></h3>

  <form name="cNew" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'Save&Process');
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_new_country');
?></p>

  <fieldset>
    <p><label for="countries_name"><?php 
echo OSCOM::get_def('field_name');
?></label><?php 
echo HTML::input_field('countries_name');
?></p>
    <p><label for="countries_iso_code_2"><?php 
echo OSCOM::get_def('field_iso_code_2');
?></label><?php 
echo HTML::input_field('countries_iso_code_2');
?></p>
    <p><label for="countries_iso_code_3"><?php 
echo OSCOM::get_def('field_iso_code_3');
?></label><?php 
echo HTML::input_field('countries_iso_code_3');
?></p>
    <p><label for="address_format"><?php 
echo OSCOM::get_def('field_address_format');
?></label><?php 
echo HTML::textarea_field('address_format');
?><br /><i>:name</i>, <i>:street_address</i>, <i>:suburb</i>, <i>:city</i>, <i>:postcode</i>, <i>:state</i>, <i>:state_code</i>, <i>:country</i></p>
  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
