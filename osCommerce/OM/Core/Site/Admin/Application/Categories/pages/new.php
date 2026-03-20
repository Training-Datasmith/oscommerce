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
  $('#cNewForm input, #cNewForm select, #cNewForm textarea, #cNewForm fileupload').safetynet();
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

<form id="cNewForm" name="cNew" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'Save&Process&cid=' . $OSCOM_Application->get_current_category_id());
?>" method="post" enctype="multipart/form-data">

<div id="formButtons" style="float: right;"><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::get_def('button_save')]) . ' ' . HTML::button(['type' => 'button', 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel'), 'params' => 'onclick="$.safetynet.suppressed(true); window.location.href=\'' . OSCOM::get_link(null, null, 'cid=' . $OSCOM_Application->get_current_category_id()) . '\';"']);
?></div>

<div style="clear: both;"></div>

<div class="infoBox">
  <h3><?php 
echo HTML::icon('new.png') . ' ' . OSCOM::get_def('action_heading_new_category');
?></h3>

  <fieldset>
    <p><label for="parent_id"><?php 
echo OSCOM::get_def('field_parent_category');
?></label><?php 
echo HTML::select_menu('parent_id', array_merge([['id' => '0', 'text' => OSCOM::get_def('top_category')]], $OSCOM_Application->get_category_list()), $OSCOM_Application->get_current_category_id());
?></p>
    <p><label><?php 
echo OSCOM::get_def('field_name');
?></label></p>

<?php 
foreach ($OSCOM_Language->get_all() as $l) {
    echo '<p>' . $OSCOM_Language->show_image($l['code']) . '&nbsp;' . $l['name'] . '<br />' . HTML::input_field('categories_name[' . $l['id'] . ']') . '</p>';
}
?>

    <p><label><?php 
echo OSCOM::get_def('field_image');
?></label></p>
    <p id="cImage" class="imageSelectorPlaceholder"></p>

    <p><label><?php 
echo OSCOM::get_def('field_image_browser');
?></label></p>
    <div class="imageSelector">
      <ul id="cImages"></ul>

      <div id="fileUploader" style="padding-top: 50px; padding-left: 20px;"></div>
    </div>
  </fieldset>
</div>

</form>

<script>
function loadImageSelector() {
  $('#cImages').imageSelector({
    json: '<?php 
echo OSCOM::get_rpc_link(null, null, 'GetAvailableImages');
?>',
    imagePath: 'public/upload',
    show: 5,
    selector: 'cImage'
  });
}

$(function() {
  loadImageSelector();

  var uploader = new qq.FileUploader({
    element: document.getElementById('fileUploader'),
    action: '<?php 
echo OSCOM::get_rpc_link(null, null, 'SaveUploadedImage');
?>',
    allowedExtensions: ['gif', 'jpg', 'png'],
    textUpload: '<?php 
echo OSCOM::get_def('button_upload_new_file');
?>',
    onComplete: function(id, fileName, responseJSON) {
      fileName = responseJSON.filename;

      loadImageSelector();

      $('#cImage').css('backgroundImage', 'none').html('<img src="public/upload/' + fileName + '" alt="' + fileName + '" title="' + fileName + '" onclick="window.open(this.src);" /><input type="hidden" name="cImageSelected" value="' + fileName + '" />');
    }
  });
});
</script>
