<?php

/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
/*
      if (DOWNLOAD_ENABLED == '1') {
        $download_query_raw ="select products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount
                              from " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                              where products_attributes_id='" . $attributes_values['products_attributes_id'] . "'";
        $download_query = tep_db_query($download_query_raw);
        if (tep_db_num_rows($download_query) > 0) {
          $download = tep_db_fetch_array($download_query);
          $products_attributes_filename = $download['products_attributes_filename'];
          $products_attributes_maxdays  = $download['products_attributes_maxdays'];
          $products_attributes_maxcount = $download['products_attributes_maxcount'];
        }
?>
          <tr class="<?php echo (!($rows % 2)? 'attributes-even' : 'attributes-odd');?>">
            <td>&nbsp;</td>
            <td colspan="5">
              <table>
                <tr class="<?php echo (!($rows % 2)? 'attributes-even' : 'attributes-odd');?>">
                  <td><?php echo TABLE_HEADING_DOWNLOAD; ?>&nbsp;</td>
                  <td><?php echo TABLE_TEXT_FILENAME; ?></td>
                  <td><?php echo tep_draw_input_field('products_attributes_filename', $products_attributes_filename, 'size="15"'); ?>&nbsp;</td>
                  <td><?php echo TABLE_TEXT_MAX_DAYS; ?></td>
                  <td><?php echo tep_draw_input_field('products_attributes_maxdays', $products_attributes_maxdays, 'size="5"'); ?>&nbsp;</td>
                  <td><?php echo TABLE_TEXT_MAX_COUNT; ?></td>
                  <td><?php echo tep_draw_input_field('products_attributes_maxcount', $products_attributes_maxcount, 'size="5"'); ?>&nbsp;</td>
                </tr>
              </table>
            </td>
            <td>&nbsp;</td>
          </tr>
<?php
      }
*/
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->size($os_c_template->get_module()) > 0) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<p align="right"><?php 
echo '<input type="button" value="' . $os_c_language->get('button_insert') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&action=save') . '\';" class="infoBoxButton" />';
?></p>

<?php 
$Qgroups = $os_c_database->query('select id, title, sort_order from :table_products_variants_groups where languages_id = :languages_id order by sort_order, title');
$Qgroups->bind_table(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
$Qgroups->bind_int(':languages_id', $os_c_language->get_id());
$Qgroups->set_batch_limit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
$Qgroups->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php 
echo $Qgroups->get_batch_total_pages($os_c_language->get('batch_results_number_of_entries'));
?></td>
    <td align="right"><?php 
echo $Qgroups->get_batch_page_links('page', $os_c_template->get_module(), false);
?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php 
echo $os_c_language->get('table_heading_attribute_groups');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_sort_order');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_total_entries');
?></th>
      <th width="150"><?php 
echo $os_c_language->get('table_heading_action');
?></th>
      <th align="center" width="20"><?php 
echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"');
?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="4"><?php 
echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $os_c_language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />';
?></th>
      <th align="center" width="20"><?php 
echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"');
?></th>
    </tr>
  </tfoot>
  <tbody>

<?php 
while ($Qgroups->next()) {
    $Qentries = $os_c_database->query('select count(*) as total from :table_products_variants_values where products_variants_groups_id = :products_variants_groups_id');
    $Qentries->bind_table(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
    $Qentries->bind_int(':products_variants_groups_id', $Qgroups->value_int('id'));
    $Qentries->execute();
    ?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td onclick="document.getElementById('batch<?php 
    echo $Qgroups->value_int('id');
    ?>').checked = !document.getElementById('batch<?php 
    echo $Qgroups->value_int('id');
    ?>').checked;"><?php 
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '=' . $Qgroups->value_int('id') . '&page=' . $_GET['page']), osc_icon('folder.png') . '&nbsp;' . $Qgroups->value('title'));
    ?></td>
      <td><?php 
    echo $Qgroups->value_int('sort_order');
    ?></td>
      <td><?php 
    echo $Qentries->value_int('total');
    ?></td>
      <td align="right">

<?php 
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&paID=' . $Qgroups->value_int('id') . '&action=save'), osc_icon('edit.png')) . '&nbsp;' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&paID=' . $Qgroups->value_int('id') . '&action=delete'), osc_icon('trash.png'));
    ?>

      </td>
      <td align="center"><?php 
    echo osc_draw_checkbox_field('batch[]', $Qgroups->value_int('id'), null, 'id="batch' . $Qgroups->value_int('id') . '"');
    ?></td>
    </tr>

<?php 
}
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php 
echo '<b>' . $os_c_language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $os_c_language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $os_c_language->get('icon_trash');
?></td>
    <td align="right"><?php 
echo $Qgroups->get_batch_pages_pull_down_menu('page', $os_c_template->get_module());
?></td>
  </tr>
</table>
