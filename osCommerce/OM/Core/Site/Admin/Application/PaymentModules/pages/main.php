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
echo HTML::button(['href' => OSCOM::get_link(null, null, 'Install'), 'icon' => 'plus', 'title' => OSCOM::get_def('button_install')]);
?></span>
</form>

<div style="padding: 20px 5px 5px 5px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="paymentModulesDataTable">
  <thead>
    <tr>
      <th><?php 
echo OSCOM::get_def('table_heading_payment_modules');
?></th>
      <th><?php 
echo OSCOM::get_def('table_heading_sort_order');
?></th>
      <th width="150"><?php 
echo OSCOM::get_def('table_heading_action');
?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="3">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

</form>

<div style="padding: 5px;">
  <span id="dataTableLegend"><?php 
echo '<b>' . OSCOM::get_def('table_action_legend') . '</b> ' . HTML::icon('edit.png') . '&nbsp;' . OSCOM::get_def('icon_edit') . '&nbsp;&nbsp;' . HTML::icon('uninstall.png') . '&nbsp;' . OSCOM::get_def('icon_uninstall');
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

  var dataTableName = 'paymentModulesDataTable';
  var dataTableDataURL = '<?php 
echo OSCOM::get_rpc_link(null, null, 'GetInstalled');
?>';

  var pmEditLink = '<?php 
echo OSCOM::get_link(null, null, 'Save&code=PMCODE');
?>';
  var pmEditLinkIcon = '<?php 
echo HTML::icon('edit.png');
?>';

  var pmUninstallLink = '<?php 
echo OSCOM::get_link(null, null, 'Uninstall&code=PMCODE');
?>';
  var pmUninstallLinkIcon = '<?php 
echo HTML::icon('uninstall.png');
?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + record.code;

      if ( record.status != true ) {
        $('#row' + record.code).addClass('deactivatedRow');
      }

      $('#row' + record.code).hover( function() { $(this).addClass('mouseOver'); }, function() { $(this).removeClass('mouseOver'); }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = htmlSpecialChars(record.title);

      newCell = newRow.insertCell(1);
      newCell.innerHTML = parseInt(record.sort_order);

      newCell = newRow.insertCell(2);
      newCell.innerHTML = '<a href="' + pmEditLink.replace('PMCODE', htmlSpecialChars(record.code)) + '">' + pmEditLinkIcon + '</a>&nbsp;<a href="' + pmUninstallLink.replace('PMCODE', htmlSpecialChars(record.code)) + '">' + pmUninstallLinkIcon + '</a>';
      newCell.align = 'right';

      rowCounter++;
    }
  }
</script>
