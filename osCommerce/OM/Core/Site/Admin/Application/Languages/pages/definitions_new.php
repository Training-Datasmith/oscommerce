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
use Os_Commerce\OM\Core\Site\Admin\Application\Languages\Languages;
$groups_array = [];
foreach (Object_Info::to(Languages::get_groups($_GET['id']))->get('entries') as $value) {
    $groups_array[] = $value['content_group'];
}
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
echo HTML::icon('new.png') . ' ' . OSCOM::get_def('action_heading_new_language_definition');
?></h3>

  <form name="lNew" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'InsertDefinition&Process&id=' . $_GET['id'] . (isset($_GET['group']) ? '&group=' . $_GET['group'] : ''));
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_new_language_definition');
?></p>

  <fieldset>
    <p><label for="key"><?php 
echo OSCOM::get_def('field_definition_key');
?></label><?php 
echo HTML::input_field('key');
?></p>
    <p><label><?php 
echo OSCOM::get_def('field_definition_value');
?></label>

<?php 
foreach ($OSCOM_Language->get_all() as $l) {
    echo '<br />' . $OSCOM_Language->show_image($l['code']) . '<br />' . HTML::textarea_field('value[' . $l['id'] . ']');
}
?>

    </p>
    <p><label for="defgroup"><?php 
echo OSCOM::get_def('field_definition_group');
?></label><?php 
echo HTML::input_field('defgroup', isset($_GET['group']) ? $_GET['group'] : null);
?></p>
  </fieldset>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['href' => OSCOM::get_link(null, null, 'id=' . $_GET['id'] . (isset($_GET['group']) ? '&group=' . $_GET['group'] : '')), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>

<script type="text/javascript">
  var suggestGroups = <?php 
echo json_encode($groups_array);
?>;

  $("#defgroup").autocomplete({
    source: suggestGroups,
    minLength: 0
  });
</script>
