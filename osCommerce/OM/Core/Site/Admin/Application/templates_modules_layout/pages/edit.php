<?php

/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
$Qtemplate = $os_c_database->query('select id from :table_templates where code = :code');
$Qtemplate->bind_table(':table_templates', TABLE_TEMPLATES);
$Qtemplate->bind_value(':code', $_GET['filter']);
$Qtemplate->execute();
$filter_id = $Qtemplate->value_int('id');
$boxes_array = [];
$Qboxes = $os_c_database->query('select id, title from :table_templates_boxes where modules_group = :modules_group order by title');
$Qboxes->bind_table(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
$Qboxes->bind_value(':modules_group', $_GET['set']);
$Qboxes->execute();
while ($Qboxes->next()) {
    $boxes_array[] = ['id' => $Qboxes->value_int('id'), 'text' => $Qboxes->value('title')];
}
$pages_array = [['id' => $filter_id . '/*', 'text' => '*']];
$d_boxes = new Os_C_directory_Listing('../templates/' . $_GET['filter'] . '/content');
$d_boxes->set_recursive(true);
$d_boxes->set_add_directory_to_filename(true);
$d_boxes->set_check_extension('php');
$d_boxes->set_exclude_entries('.svn');
foreach ($d_boxes->get_files(false) as $box) {
    if ($box['is_directory'] === true) {
        $entry = ['id' => $filter_id . '/' . $box['name'] . '/*', 'text' => $box['name'] . '/*'];
    } else {
        $page_filename = substr($box['name'], 0, strrpos($box['name'], '.'));
        $entry = ['id' => $filter_id . '/' . $page_filename, 'text' => $page_filename];
    }
    if ($_GET['filter'] != DEFAULT_TEMPLATE && $d_boxes->get_size() > 0) {
        $entry['group'] = '-- ' . $_GET['filter'] . ' --';
    }
    $pages_array[] = $entry;
}
if ($_GET['filter'] != DEFAULT_TEMPLATE) {
    $d_boxes = new Os_C_directory_Listing('../templates/' . DEFAULT_TEMPLATE . '/content');
    $d_boxes->set_recursive(true);
    $d_boxes->set_add_directory_to_filename(true);
    $d_boxes->set_check_extension('php');
    $d_boxes->set_exclude_entries('.svn');
    foreach ($d_boxes->get_files(false) as $box) {
        if ($box['is_directory'] === true) {
            $entry = ['id' => $filter_id . '/' . $box['name'] . '/*', 'text' => $box['name'] . '/*'];
        } else {
            $page_filename = substr($box['name'], 0, strrpos($box['name'], '.'));
            $entry = ['id' => $filter_id . '/' . $page_filename, 'text' => $page_filename];
        }
        $check_entry = $entry;
        $check_entry['group'] = '-- ' . $_GET['filter'] . ' --';
        if (!in_array($check_entry, $pages_array)) {
            $entry['group'] = '-- ' . DEFAULT_TEMPLATE . ' --';
            $pages_array[] = $entry;
        }
    }
}
require 'includes/templates/' . $_GET['filter'] . '.php';
$class = 'osC_Template_' . $_GET['filter'];
$filter_template = new $class();
$groups_array = [];
foreach ($filter_template->get_groups($_GET['set']) as $group) {
    $groups_array[] = ['id' => $group, 'text' => $group];
}
$Qgroups = $os_c_database->query('select distinct b2p.boxes_group from :table_templates_boxes_to_pages b2p, :table_templates_boxes b where b2p.templates_id = :templates_id and b2p.templates_boxes_id = b.id and b.modules_group = :modules_group and b2p.boxes_group not in (:boxes_group) order by b2p.boxes_group');
$Qgroups->bind_table(':table_templates_boxes_to_pages', TABLE_TEMPLATES_BOXES_TO_PAGES);
$Qgroups->bind_table(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
$Qgroups->bind_int(':templates_id', $filter_id);
$Qgroups->bind_value(':modules_group', $_GET['set']);
$Qgroups->bind_raw(':boxes_group', '"' . implode('", "', $filter_template->get_groups($_GET['set'])) . '"');
$Qgroups->execute();
while ($Qgroups->next()) {
    $groups_array[] = ['id' => $Qgroups->value('boxes_group'), 'text' => $Qgroups->value('boxes_group')];
}
if (!empty($groups_array)) {
    array_unshift($groups_array, ['id' => null, 'text' => $os_c_language->get('please_select')]);
}
$Qlayout = $os_c_database->query('select b2p.*, b.title as box_title, b.code as box_code from :table_templates_boxes_to_pages b2p, :table_templates_boxes b where b2p.id = :id and b2p.templates_boxes_id = b.id');
$Qlayout->bind_table(':table_templates_boxes_to_pages', TABLE_TEMPLATES_BOXES_TO_PAGES);
$Qlayout->bind_table(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
$Qlayout->bind_int(':id', $_GET['lID']);
$Qlayout->execute();
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter']), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->size($os_c_template->get_module()) > 0) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<div class="infoBoxHeading"><?php 
echo osc_icon('edit.png') . ' ' . $Qlayout->value('box_title');
?></div>
<div class="infoBoxContent">
  <form name="lEdit" action="<?php 
echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter'] . '&lID=' . $Qlayout->value_int('id') . '&action=save');
?>" method="post">

  <p><?php 
echo $os_c_language->get('introduction_edit_template_layout_module');
?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_module') . '</b>';
?></td>
      <td width="60%"><?php 
echo $Qlayout->value('box_title') . osc_draw_hidden_field('box', $Qlayout->value('box_code'));
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_pages') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_pull_down_menu('content_page', $pages_array, $Qlayout->value_int('templates_id') . '/' . $Qlayout->value('content_page'), 'style="width: 100%;"');
?></td>
    </tr>
    <tr>
      <td width="40%">&nbsp;</td>
      <td width="60%"><?php 
echo osc_draw_checkbox_field('page_specific', null, $Qlayout->value_int('page_specific') === 1 ? true : false) . '&nbsp;<b>' . $os_c_language->get('field_page_specific') . '</b>';
?></td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_group') . '</b>';
?></td>
      <td width="60%">

<?php 
if (!empty($groups_array)) {
    echo osc_draw_pull_down_menu('group', $groups_array, $Qlayout->value('boxes_group'), 'style="width: 30%;"') . '&nbsp;&nbsp;<b>' . $os_c_language->get('field_group_new') . '</b>&nbsp;';
}
echo osc_draw_input_field('group_new', null, 'style="width: ' . (empty($groups_array) ? '100%' : '40%') . ';"');
?>

      </td>
    </tr>
    <tr>
      <td width="40%"><?php 
echo '<b>' . $os_c_language->get('field_sort_order') . '</b>';
?></td>
      <td width="60%"><?php 
echo osc_draw_input_field('sort_order', $Qlayout->value_int('sort_order'), 'style="width: 100%;"');
?></td>
    </tr>
  </table>

  <p align="center"><?php 
echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter']) . '\';" class="operationButton" />';
?></p>

  </form>
</div>
