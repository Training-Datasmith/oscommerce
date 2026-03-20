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

<div id="sectionMenu_password">
  <div class="infoBox">

<?php 
if ($new_customer) {
    echo '<h3>' . HTML::icon('new.png') . ' ' . OSCOM::get_def('action_heading_new_customer') . '</h3>';
} else {
    echo '<h3>' . HTML::icon('edit.png') . ' ' . $oscom_object_info->get_protected('customers_name') . '</h3>';
}
?>

    <fieldset>
      <p><label for="password"><?php 
echo OSCOM::get_def('field_new_password');
?></label><?php 
echo HTML::password_field('password');
?></p>
      <p><label for="confirmation"><?php 
echo OSCOM::get_def('field_new_password_confirmation');
?></label><?php 
echo HTML::password_field('confirmation');
?></p>
    </fieldset>
  </div>
</div>
