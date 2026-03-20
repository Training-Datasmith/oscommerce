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
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->size($os_c_template->get_module()) > 0) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
$Qreviews = $os_c_database->query('select r.reviews_id, r.products_id, r.date_added, r.last_modified, r.reviews_rating, r.reviews_status, pd.products_name, l.code as languages_code from :table_reviews r left join :table_products_description pd on (r.products_id = pd.products_id and r.languages_id = pd.language_id), :table_languages l where r.languages_id = l.languages_id order by r.date_added desc');
$Qreviews->bind_table(':table_reviews', TABLE_REVIEWS);
$Qreviews->bind_table(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
$Qreviews->bind_table(':table_languages', TABLE_LANGUAGES);
$Qreviews->set_batch_limit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
$Qreviews->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php 
echo $Qreviews->get_batch_total_pages($os_c_language->get('batch_results_number_of_entries'));
?></td>
    <td align="right"><?php 
echo $Qreviews->get_batch_page_links('page', $os_c_template->get_module(), false);
?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php 
echo $os_c_language->get('table_heading_products');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_language');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_rating');
?></th>
      <th><?php 
echo $os_c_language->get('table_heading_date_added');
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
      <th align="right" colspan="5"><?php 
echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $os_c_language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />';
?></th>
      <th align="center" width="20"><?php 
echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"');
?></th>
    </tr>
  </tfoot>
  <tbody>

<?php 
while ($Qreviews->next()) {
    if (defined('SERVICE_REVIEW_ENABLE_MODERATION') && SERVICE_REVIEW_ENABLE_MODERATION != -1) {
        echo '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" ' . ($Qreviews->value_int('reviews_status') !== 1 ? 'class="deactivatedRow"' : '') . '>';
    } else {
        echo '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">';
    }
    ?>

      <td onclick="document.getElementById('batch<?php 
    echo $Qreviews->value_int('reviews_id');
    ?>').checked = !document.getElementById('batch<?php 
    echo $Qreviews->value_int('reviews_id');
    ?>').checked;"><?php 
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&rID=' . $Qreviews->value_int('reviews_id') . '&action=preview'), osc_icon('reviews.png') . '&nbsp;' . $Qreviews->value('products_name'));
    ?></td>
      <td align="center"><?php 
    echo $os_c_language->show_image($Qreviews->value('languages_code'));
    ?></td>
      <td align="center"><?php 
    echo osc_image('../images/stars_' . $Qreviews->value_int('reviews_rating') . '.png', sprintf($os_c_language->get('rating_from_5_stars'), $Qreviews->value_int('reviews_rating')));
    ?></td>
      <td><?php 
    echo Os_C_date_Time::get_short($Qreviews->value('date_added'));
    ?></td>
      <td align="right">

<?php 
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&rID=' . $Qreviews->value_int('reviews_id') . '&action=save'), osc_icon('edit.png')) . '&nbsp;' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&page=' . $_GET['page'] . '&rID=' . $Qreviews->value_int('reviews_id') . '&action=delete'), osc_icon('trash.png'));
    ?>

      </td>
      <td align="center"><?php 
    echo osc_draw_checkbox_field('batch[]', $Qreviews->value_int('reviews_id'), null, 'id="batch' . $Qreviews->value_int('reviews_id') . '"');
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
echo $Qreviews->get_batch_pages_pull_down_menu('page', $os_c_template->get_module());
?></td>
  </tr>
</table>
