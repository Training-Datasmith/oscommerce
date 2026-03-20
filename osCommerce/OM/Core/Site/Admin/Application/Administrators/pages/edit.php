<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\Object_Info;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Administrators\Administrators;
$oscom_object_info = new Object_Info(Administrators::get($_GET['id']));
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
echo HTML::icon('edit.png') . ' ' . $oscom_object_info->get_protected('user_name');
?></h3>

  <form name="aEdit" class="dataForm" autocomplete="off" action="<?php 
echo OSCOM::get_link(null, null, 'Save&Process&id=' . $oscom_object_info->get_int('id'));
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_edit_administrator');
?></p>

  <fieldset>
    <p><label for="user_name"><?php 
echo OSCOM::get_def('field_username');
?></label><?php 
echo HTML::input_field('user_name', $oscom_object_info->get('user_name'));
?></p>
    <p><label for="user_password"><?php 
echo OSCOM::get_def('field_password');
?></label><?php 
echo HTML::password_field('user_password');
?></p>

    <p><select name="accessModules" id="modulesList"><option value="-1" disabled="disabled">-- Access Modules --</option><option value="0"><?php 
echo OSCOM::get_def('global_access');
?></option></select></p>

    <ul id="accessToModules" class="modulesListing"></ul>
  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>

<script type="text/javascript">
  var accessModules = <?php 
echo json_encode(Administrators::get_access_modules());
?>;
  var hasAccessTo = <?php 
echo json_encode($oscom_object_info->get('access_modules'));
?>;
  var deleteAccessModuleIcon = '<?php 
echo HTML::icon('uninstall.png');
?>';

  var $modulesList = $('#modulesList');

  $.each(accessModules, function(i, item) {
    var sGroup = document.createElement('optgroup');
    sGroup.label = i;

    $.each(item, function(key, value) {
      var sOption = new Option(value['text'], value['id']);
      sOption.id = 'am' + value['id'];

      sGroup.appendChild(sOption);

      if ( $.inArray(value['id'], hasAccessTo) != -1 ) {
        $('#accessToModules').append('<li id="atm' + value['id'] + '">' + i + ' &raquo; ' + value['text'] + ' <span style="float: right;"><a href="#" onclick="removeAccessToModule(\'' + value['id'] + '\');">' + deleteAccessModuleIcon + '</a><input type="hidden" name="modules[]" value="' + value['id'] + '" /></span></li>');
        sOption.disabled = 'disabled';
      }
    });

    $modulesList.append(sGroup); 
  });

  if ( $.inArray('*', hasAccessTo) != -1 ) {
    $('#modulesList').val('0');

    $('#accessToModules').append('<li id="atm' + $('#modulesList :selected').val() + '">' + $('#modulesList :selected').text() + ' <span style="float: right;"><a href="#" onclick="removeAccessToModule(\'' + $('#modulesList :selected').val() + '\');">' + deleteAccessModuleIcon + '</a><input type="hidden" name="modules[]" value="' + $('#modulesList :selected').val() + '" /></span></li>');

    $('#modulesList').attr('disabled', 'disabled');
    $('#modulesList').val('-1');
  }

  $('#modulesList').change(function() {
    if ( $('#modulesList :selected').val() == '0' ) {
      $('#accessToModules li').remove();
      $('#accessToModules').append('<li id="atm' + $('#modulesList :selected').val() + '">' + $('#modulesList :selected').text() + ' <span style="float: right;"><a href="#" onclick="removeAccessToModule(\'' + $('#modulesList :selected').val() + '\');">' + deleteAccessModuleIcon + '</a><input type="hidden" name="modules[]" value="' + $('#modulesList :selected').val() + '" /></span></li>');

      $('#modulesList').attr('disabled', 'disabled');
      $('#modulesList').val('-1');

      $('#accessToModules li').tsort();
    } else if ( $('#modulesList :selected').val() != '-1' ) {
      $('#accessToModules').append('<li id="atm' + $('#modulesList :selected').val() + '">' + $('#modulesList :selected').parent().attr('label') + ' &raquo; ' + $('#modulesList :selected').text() + ' <span style="float: right;"><a href="#" onclick="removeAccessToModule(\'' + $('#modulesList :selected').val() + '\');">' + deleteAccessModuleIcon + '</a><input type="hidden" name="modules[]" value="' + $('#modulesList :selected').val() + '" /></span></li>');

      $('#modulesList :selected').attr('disabled', 'disabled');
      $('#modulesList').val('-1');

      $('#accessToModules li').tsort();
    }
  });

  function removeAccessToModule(module) {
    if ( module == '0' ) {
      $('#modulesList').removeAttr('disabled');
      $('#modulesList :disabled').removeAttr('disabled');
      $('#modulesList :first').attr('disabled', 'disabled');
    } else {
      $('#am' + module).removeAttr('disabled');
    }

    $('#atm' + module).remove();
  }
</script>
