<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Administrators\Administrators;
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
echo HTML::icon('edit.png') . ' ' . OSCOM::get_def('action_heading_batch_edit_administrators');
?></h3>

  <form name="aEditBatch" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'BatchSave&Process');
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_batch_edit_administrators');
?></p>

<?php 
$Qadmins = $OSCOM_PDO->query('select id, user_name from :table_administrators where id in ("' . implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))) . '") order by user_name');
$Qadmins->execute();
$names_string = '';
while ($Qadmins->fetch()) {
    $names_string .= HTML::hidden_field('batch[]', $Qadmins->value_int('id')) . '<b>' . $Qadmins->value_protected('user_name') . '</b>, ';
}
if (!empty($names_string)) {
    $names_string = substr($names_string, 0, -2);
}
echo '<p>' . $names_string . '</p>';
?>

  <fieldset>
    <p><?php 
echo HTML::radio_field('mode', [['id' => Administrators::ACCESS_MODE_ADD, 'text' => OSCOM::get_def('add_to')], ['id' => Administrators::ACCESS_MODE_REMOVE, 'text' => OSCOM::get_def('remove_from')], ['id' => Administrators::ACCESS_MODE_SET, 'text' => OSCOM::get_def('set_to')]], Administrators::ACCESS_MODE_ADD);
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
    });

    $modulesList.append(sGroup); 
  });

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
