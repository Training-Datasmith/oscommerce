<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
$services = [['id' => 'oanda', 'text' => 'Oanda (http://www.oanda.com)'], ['id' => 'xe', 'text' => 'XE (http://www.xe.com)']];
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
echo HTML::icon('update.png') . ' ' . OSCOM::get_def('action_heading_update_rates');
?></h3>

  <form name="cUpdate" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'UpdateRates&Process');
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_update_exchange_rates');
?></p>

  <fieldset>
    <p><?php 
echo HTML::radio_field('service', $services, null, null, '<br />');
?></p>
  </fieldset>

  <p><?php 
echo OSCOM::get_def('service_terms_agreement');
?></p>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'refresh', 'title' => OSCOM::get_def('button_update')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
