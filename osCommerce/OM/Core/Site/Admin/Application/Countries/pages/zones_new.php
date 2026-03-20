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
echo HTML::icon('new.png') . ' ' . OSCOM::get_def('action_heading_new_zone');
?></h3>

  <form name="zNew" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'ZoneSave&Process&id=' . $_GET['id']);
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_new_zone');
?></p>

  <fieldset>
    <p><label for="zone_name"><?php 
echo OSCOM::get_def('field_zone_name');
?></label><?php 
echo HTML::input_field('zone_name');
?></p>
    <p><label for="zone_code"><?php 
echo OSCOM::get_def('field_zone_code');
?></label><?php 
echo HTML::input_field('zone_code');
?></p>
  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
