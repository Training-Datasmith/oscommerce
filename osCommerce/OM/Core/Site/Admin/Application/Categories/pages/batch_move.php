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

<script>
$(function() {
  $('#cMoveBatchForm input, #cMoveBatchForm select, #cMoveBatchForm textarea, #cMoveBatchForm fileupload').safetynet();
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

<form id="cMoveBatchForm" name="cMoveBatch" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'BatchMove&Process&cid=' . $OSCOM_Application->get_current_category_id());
?>" method="post">

<div id="formButtons" style="float: right;"><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['type' => 'button', 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel'), 'params' => 'onclick="$.safetynet.suppressed(true); window.location.href=\'' . OSCOM::get_link(null, null, 'cid=' . $OSCOM_Application->get_current_category_id()) . '\';"']);
?></div>

<div style="clear: both;"></div>

<div class="infoBox">
  <h3><?php 
echo HTML::icon('move.png') . ' ' . OSCOM::get_def('action_heading_batch_move_categories');
?></h3>

  <p><?php 
echo OSCOM::get_def('introduction_batch_move_categories');
?></p>

  <fieldset>

<?php 
$categories = '';
foreach ($_POST['batch'] as $c) {
    $categories .= HTML::hidden_field('batch[]', $c) . '<b>' . $oscom_category_tree->get_data($c, 'name') . '</b>, ';
}
if (!empty($categories)) {
    $categories = substr($categories, 0, -2);
}
echo '<p>' . $categories . '</p>';
?>

    <p><label for="parent_id"><?php 
echo OSCOM::get_def('field_parent_category');
?></label><?php 
echo HTML::select_menu('parent_id', array_merge([['id' => '0', 'text' => OSCOM::get_def('top_category')]], $OSCOM_Application->get_category_list()), $OSCOM_Application->get_current_category_id());
?></p>
  </fieldset>
</div>

</form>
