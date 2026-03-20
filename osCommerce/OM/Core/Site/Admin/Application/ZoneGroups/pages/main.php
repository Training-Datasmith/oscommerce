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

<?php 
if ($oscom_message_stack->exists()) {
    echo $oscom_message_stack->get();
}
?>

<form id="liveSearchForm">
  <?php 
echo HTML::input_field('search', null, 'id="liveSearchField" class="searchField" placeholder="' . OSCOM::get_def('placeholder_search') . '"') . HTML::button(['type' => 'button', 'params' => 'onclick="osC_DataTable.reset();"', 'title' => OSCOM::get_def('button_reset')]);
?>

  <span style="float: right;"><?php 
echo HTML::button(['href' => OSCOM::get_link(null, null, 'Save'), 'icon' => 'plus', 'title' => OSCOM::get_def('button_insert')]);
?></span>
</form>

<div style="padding: 20px 5px 5px 5px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="zoneGroupsDataTable">
  <thead>
    <tr>
      <th><?php 
echo OSCOM::get_def('table_heading_zone_groups');
?></th>
      <th width="150"><?php 
echo OSCOM::get_def('table_heading_action');
?></th>
      <th align="center" width="20"><?php 
echo HTML::checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"');
?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="2"><?php 
echo HTML::submit_image(HTML::icon_raw('trash.png'), OSCOM::get_def('icon_trash'), 'onclick="document.batch.action=\'' . OSCOM::get_link(null, null, 'BatchDelete') . '\';"');
?></th>
      <th align="center" width="20"><?php 
echo HTML::checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"');
?></th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

</form>

<div style="padding: 2px;">
  <span id="dataTableLegend"><?php 
echo '<b>' . OSCOM::get_def('table_action_legend') . '</b> ' . HTML::icon('edit.png') . '&nbsp;' . OSCOM::get_def('icon_edit') . '&nbsp;&nbsp;' . HTML::icon('trash.png') . '&nbsp;' . OSCOM::get_def('icon_trash');
?></span>
  <span id="batchPullDownMenu"></span>
</div>

<script type="text/javascript">
  var moduleParamsCookieName = 'oscom_admin_' + pageModule;
  var dataTablePageSetName = 'page';

  var moduleParams = new Object();
  moduleParams[dataTablePageSetName] = 1;
  moduleParams['search'] = '';

  if ( $.cookie(moduleParamsCookieName) != null ) {
    moduleParams = $.secureEvalJSON($.cookie(moduleParamsCookieName));
  }

  var dataTableName = 'zoneGroupsDataTable';
  var dataTableDataURL = '<?php 
echo OSCOM::get_rpc_link(null, null, 'GetAll');
?>';

  var groupLink = '<?php 
echo OSCOM::get_link(null, null, 'id=GROUPID');
?>';
  var groupLinkIcon = '<?php 
echo HTML::icon('folder.png');
?>';

  var groupEditLink = '<?php 
echo OSCOM::get_link(null, null, 'Save&id=GROUPID');
?>';
  var groupEditLinkIcon = '<?php 
echo HTML::icon('edit.png');
?>';

  var groupDeleteLink = '<?php 
echo OSCOM::get_link(null, null, 'Delete&id=GROUPID');
?>';
  var groupDeleteLinkIcon = '<?php 
echo HTML::icon('trash.png');
?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + parseInt(record.geo_zone_id);

      $('#row' + parseInt(record.geo_zone_id)).hover( function() { $(this).addClass('mouseOver'); }, function() { $(this).removeClass('mouseOver'); }).click(function(event) {
        if (event.target.type !== 'checkbox') {
          $(':checkbox', this).trigger('click');
        }
      }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = groupLinkIcon + '&nbsp;<a href="' + groupLink.replace('GROUPID', parseInt(record.geo_zone_id)) + '" class="parent">' + htmlSpecialChars(record.geo_zone_name) + '</a><span style="float: right;">(' + parseInt(record.total_entries) + ')</span>';

      newCell = newRow.insertCell(1);
      newCell.innerHTML = '<a href="' + groupEditLink.replace('GROUPID', parseInt(record.geo_zone_id)) + '">' + groupEditLinkIcon + '</a>&nbsp;<a href="' + groupDeleteLink.replace('GROUPID', parseInt(record.geo_zone_id)) + '">' + groupDeleteLinkIcon + '</a>';
      newCell.align = 'right';

      newCell = newRow.insertCell(2);
      newCell.innerHTML = '<input type="checkbox" name="batch[]" value="' + parseInt(record.geo_zone_id) + '" id="batch' + parseInt(record.geo_zone_id) + '" />';
      newCell.align = 'center';

      rowCounter++;
    }
  }
</script>
