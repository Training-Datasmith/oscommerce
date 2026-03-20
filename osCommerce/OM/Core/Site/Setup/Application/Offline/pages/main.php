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

<div class="mainBlock">
  <ul style="list-style-type: none; padding: 5px; margin: 0px; display: inline; float: right;">
    <li style="font-weight: bold; display: inline;"><?php 
echo OSCOM::get_def('title_language');
?></li>

<?php 
foreach ($OSCOM_Language->get_all() as $available_language) {
    ?>

    <li style="display: inline;"><?php 
    echo '<a href="' . OSCOM::get_link(null, null, 'language=' . $available_language['code']) . '">' . $OSCOM_Language->show_image($available_language['code']) . '</a>';
    ?></li>

<?php 
}
?>

  </ul>

  <h1><?php 
echo OSCOM::get_def('page_title_authorization_required');
?></h1>
</div>

<div class="contentBlock">
  <div class="contentPane" style="margin-left: 0;">
    <h2><?php 
echo OSCOM::get_def('page_heading_access_disabled');
?></h2>

    <p><?php 
echo OSCOM::get_def('text_access_disabled');
?></p>

    <p align="center"><?php 
echo HTML::button(['href' => OSCOM::get_link(null, OSCOM::get_default_site_application()), 'priority' => 'primary', 'icon' => 'triangle-1-e', 'title' => OSCOM::get_def('button_continue')]);
?></p>
  </div>
</div>
