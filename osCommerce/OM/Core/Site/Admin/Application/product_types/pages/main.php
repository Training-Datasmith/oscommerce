<?php

/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->exists($os_c_template->get_module())) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<div>
  <span style="float: left;"><form id="liveSearchForm"><input type="text" id="liveSearchField" name="search" class="searchField fieldTitleAsDefault" title="Search.." /><?php 
echo osc_draw_button(['type' => 'button', 'params' => 'onclick="osC_DataTable.reset();"', 'title' => 'Reset', 'radius' => 'right']);
?></form></span>
  <span style="float: right;"><?php 
echo osc_draw_button(['href' => osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&action=save'), 'icon' => 'plus', 'title' => OSCOM::get_def('button_insert')]);
?></span>
</div>

<div class="dataTableHeader">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="productTypesDataTable">
  <thead>
    <tr>
      <th><?php 
echo OSCOM::get_def('table_heading_product_types');
?></th>
      <th width="150"><?php 
echo OSCOM::get_def('table_heading_action');
?></th>
      <th align="center" width="20"><?php 
echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"');
?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="2"><?php 
echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . OSCOM::get_def('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&action=batchDelete') . '\';" />';
?></th>
      <th align="center" width="20"><?php 
echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"');
?></th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

</form>

<div class="dataTableFooter">
  <span id="dataTableLegend"><?php 
echo '<b>' . OSCOM::get_def('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . OSCOM::get_def('icon_edit') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . OSCOM::get_def('icon_trash');
?></span>
  <span id="batchPullDownMenu"></span>
</div>

<script type="text/javascript">
  var moduleParamsCookieName = 'oscadmin_module_' + pageModule;

  var moduleParams = new Object();
  moduleParams.page = 1;
  moduleParams.search = '';

  if ( $.cookie(moduleParamsCookieName) != null ) {
    var p = $.secureEvalJSON($.cookie(moduleParamsCookieName));
    moduleParams.page = parseInt(p.page);
    moduleParams.search = String(p.search);
  }

  var dataTableName = 'productTypesDataTable';
  var dataTableDataURL = '<?php 
echo osc_href_link_admin('rpc.php', $os_c_template->get_module() . '&action=getAll');
?>';

  var typeLink = '<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '=TYPEID');
?>';
  var typeLinkIcon = '<?php 
echo osc_icon('folder.png');
?>';

  var typeEditLink = '<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&tID=TYPEID&action=save');
?>';
  var typeEditLinkIcon = '<?php 
echo osc_icon('edit.png');
?>';

  var typeDeleteLink = '<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&tID=TYPEID&action=delete');
?>';
  var typeDeleteLinkIcon = '<?php 
echo osc_icon('trash.png');
?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + parseInt(record.id);

      $('#row' + parseInt(record.id)).mouseover( function() { $(this).addClass('mouseOver'); }).mouseout( function() { $(this).removeClass('mouseOver'); }).click(function(event) {
        if (event.target.type !== 'checkbox') {
          $(':checkbox', this).trigger('click');
        }
      }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = typeLinkIcon + '&nbsp;<a href="' + typeLink.replace('TYPEID', parseInt(record.id)) + '" class="parent">' + htmlSpecialChars(record.title) + '</a><span style="float: right;">(' + parseInt(record.total_assignments) + ')</span>';

      newCell = newRow.insertCell(1);
      newCell.innerHTML = '<a href="' + typeEditLink.replace('TYPEID', parseInt(record.id)) + '">' + typeEditLinkIcon + '</a>&nbsp;<a href="' + typeDeleteLink.replace('TYPEID', parseInt(record.id)) + '">' + typeDeleteLinkIcon + '</a>';
      newCell.align = 'right';

      newCell = newRow.insertCell(2);
      newCell.innerHTML = '<input type="checkbox" name="batch[]" value="' + parseInt(record.id) + '" id="batch' + parseInt(record.id) + '" />';
      newCell.align = 'center';

      rowCounter++;
    }
  }
</script>
