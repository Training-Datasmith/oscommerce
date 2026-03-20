<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
$new_customer = true;
if (ACCOUNT_GENDER > -1) {
    $gender_array = [['id' => 'm', 'text' => OSCOM::get_def('gender_male')], ['id' => 'f', 'text' => OSCOM::get_def('gender_female')]];
}
?>

<script>
$(function() {
  $('#cEditForm input, #cEditForm select, #cEditForm textarea, #cEditForm fileupload').safetynet();
});
</script>

<h1><?php 
echo $OSCOM_Template->get_icon(32) . HTML::link(OSCOM::get_link(), $OSCOM_Template->get_page_title());
?></h1>

<?php 
if ($oscom_message_stack->exists()) {
    echo $oscom_message_stack->get();
}
?>

<div id="sectionMenuContainer" style="float: left; padding-bottom: 10px;">
  <span class="ui-widget-header ui-corner-all" style="padding: 10px 4px;">
    <span id="sectionMenu"><?php 
echo HTML::radio_field('sections', [['id' => 'personal', 'text' => OSCOM::get_def('section_personal')], ['id' => 'password', 'text' => OSCOM::get_def('section_password')], ['id' => 'addressBook', 'text' => OSCOM::get_def('section_address_book')], ['id' => 'newsletters', 'text' => OSCOM::get_def('section_newsletters')], ['id' => 'map', 'text' => OSCOM::get_def('section_map')], ['id' => 'social', 'text' => OSCOM::get_def('section_social')]], isset($_GET['tabIndex']) ? $_GET['tabIndex'] : null, null, '');
?></span>
  </span>
</div>

<script>
$(function() {
  $('#sectionMenu').buttonsetTabs();
});
</script>

<form id="cEditForm" name="cEdit" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'Save&Process');
?>" method="post">

<div id="formButtons" style="float: right;"><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['type' => 'button', 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel'), 'params' => 'onclick="$.safetynet.suppressed(true); window.location.href=\'' . OSCOM::get_link() . '\';"']);
?></div>

<div style="clear: both;"></div>

<?php 
// HPDL Modularize, zack zack!
include 'section_personal.php';
include 'section_password.php';
include 'section_addressBook.php';
include 'section_newsletters.php';
include 'section_map.php';
include 'section_social.php';
?>

</form>
