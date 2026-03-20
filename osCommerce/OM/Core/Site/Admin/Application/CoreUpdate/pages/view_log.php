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
echo HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'triangle-1-w', 'title' => OSCOM::get_def('button_back')]);
?></span>
</form>

<div style="padding: 20px 5px 5px 5px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="logDataTable">
  <thead>
    <tr>
      <th><?php 
echo OSCOM::get_def('table_heading_log_date');
?></th>
      <th><?php 
echo OSCOM::get_def('table_heading_log_message');
?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="2">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

<div style="padding: 5px;">
  <span id="dataTableLegend"></span>
  <span id="batchPullDownMenu"></span>
</div>

<script>
  var moduleParamsCookieName = 'oscom_admin_' + pageModule;
  var dataTablePageSetName = 'log_page';

  var moduleParams = new Object();
  moduleParams[dataTablePageSetName] = 1;
  moduleParams['search'] = '';

  if ( $.cookie(moduleParamsCookieName) != null ) {
    moduleParams = $.secureEvalJSON($.cookie(moduleParamsCookieName));
  }

  var dataTableName = 'logDataTable';
  var dataTableDataURL = '<?php 
echo OSCOM::get_rpc_link(null, null, 'GetLog&log=' . $_GET['log']);
?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + parseInt(rowCounter);

      $('#row' + parseInt(rowCounter)).hover( function() { $(this).addClass('mouseOver'); }, function() { $(this).removeClass('mouseOver'); }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = htmlSpecialChars(record.date);

      var newCell = newRow.insertCell(1);
      newCell.innerHTML = htmlSpecialChars(record.message);

      rowCounter++;
    }
  }
</script>
