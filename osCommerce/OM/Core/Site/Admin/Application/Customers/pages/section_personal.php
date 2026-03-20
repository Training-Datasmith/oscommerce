<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\DateTime;
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
?>

<div id="sectionMenu_personal">
  <div class="infoBox">

<?php 
if ($new_customer) {
    echo '<h3>' . HTML::icon('new.png') . ' ' . OSCOM::get_def('action_heading_new_customer') . '</h3>';
} else {
    echo '<h3>' . HTML::icon('edit.png') . ' ' . $oscom_object_info->get_protected('customers_name') . '</h3>';
}
?>

    <fieldset>

<?php 
if (ACCOUNT_GENDER > -1) {
    ?>

      <p id="genderFields"><label for="gender"><?php 
    echo OSCOM::get_def('field_gender');
    ?></label><?php 
    echo HTML::radio_field('gender', $gender_array, $new_customer ? 'm' : $oscom_object_info->get('customers_gender'), null, '');
    ?></p>

      <script>$('#genderFields').buttonset();</script>

<?php 
}
?>

      <p><label for="firstname"><?php 
echo OSCOM::get_def('field_first_name');
?></label><?php 
echo HTML::input_field('firstname', $new_customer ? null : $oscom_object_info->get('customers_firstname'));
?></p>
      <p><label for="lastname"><?php 
echo OSCOM::get_def('field_last_name');
?></label><?php 
echo HTML::input_field('lastname', $new_customer ? null : $oscom_object_info->get('customers_lastname'));
?></p>

<?php 
if (ACCOUNT_DATE_OF_BIRTH == '1') {
    ?>

      <p><label for="dob"><?php 
    echo OSCOM::get_def('field_date_of_birth');
    ?></label><?php 
    echo HTML::input_field('dob', $new_customer ? null : DateTime::from_unix_timestamp(DateTime::get_timestamp($oscom_object_info->get('customers_dob')), 'Y-m-d'));
    ?></p>

      <script>$('#dob').datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true, yearRange: '-100:+0'});</script>

<?php 
}
?>

      <p><label for="email_address"><?php 
echo OSCOM::get_def('field_email_address');
?></label><?php 
echo HTML::input_field('email_address', $new_customer ? null : $oscom_object_info->get('customers_email_address'));
?></p>
      <p><label for="status"><?php 
echo OSCOM::get_def('field_status');
?></label><?php 
echo HTML::checkbox_field('status', null, $new_customer ? true : $oscom_object_info->get('customers_status') == '1');
?></p>
    </fieldset>
  </div>
</div>
