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

<div id="sectionMenu_newsletters">
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
if (ACCOUNT_NEWSLETTER == '1') {
    ?>

      <p><label for="newsletter"><?php 
    echo OSCOM::get_def('field_newsletter_subscription');
    ?></label><?php 
    echo HTML::checkbox_field('newsletter', null, $new_customer ? null : $oscom_object_info->get('customers_newsletter') == '1');
    ?></p>

<?php 
}
?>

    </fieldset>
  </div>
</div>
