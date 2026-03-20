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

<div class="infoBox">
  <h3><?php 
echo HTML::icon('people.png') . ' ' . OSCOM::get_def('action_heading_login');
?></h3>

  <form id="formLogin" name="login" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'Process');
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction');
?></p>

  <fieldset>
    <p><label for="user_name"><?php 
echo OSCOM::get_def('field_username');
?></label><?php 
echo HTML::input_field('user_name', null, 'tabindex="1"');
?></p>
    <p><label for="user_password"><?php 
echo OSCOM::get_def('field_password');
?></label><?php 
echo HTML::password_field('user_password', 'tabindex="2"');
?></p>
  </fieldset>

  <p><?php 
echo HTML::button(['icon' => 'key', 'title' => OSCOM::get_def('button_login')]);
?></p>

  </form>
</div>

<script type="text/javascript">
  $('#user_name').focus();

  if (typeof webkitNotifications != 'undefined') {
    $('#formLogin').submit(function() {
      if ( webkitNotifications.checkPermission() == 1 ) {
        webkitNotifications.requestPermission();
      }
    });
  }
</script>

<?php 
if (isset($_GET['Process']) && !empty($_POST['user_name']) && !empty($_POST['user_password'])) {
    ?>

<script type="text/javascript" src="public/external/jquery/jquery.showPasswordCheckbox.js"></script>
<script type="text/javascript">
  var showPasswordText = '<?php 
    echo addslashes(OSCOM::get_def('field_show_password'));
    ?>';
  $("#user_password").showPasswordCheckbox();
</script>

<?php 
}