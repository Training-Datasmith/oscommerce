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
include 'includes/modules/image/' . $_GET['module'] . '.php';
$class = 'osC_Image_Admin_' . $_GET['module'];
$os_c_images = new $class();
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title() . ': ' . $os_c_images->get_title());
?></h1>

<?php 
if ($os_c_message_stack->size($os_c_template->get_module()) > 0) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<p align="right"><?php 
echo '<input type="button" value="' . $os_c_language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()) . '\';" class="operationButton" />';
?></p>

<?php 
if (!isset($_POST['subaction']) && $os_c_images->has_parameters()) {
    ?>

<div class="infoBoxHeading"><?php 
    echo osc_icon('edit.png') . ' ' . $os_c_images->get_title();
    ?></div>
<div class="infoBoxContent">
  <form name="iEdit" action="<?php 
    echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&module=' . $os_c_images->get_module_code());
    ?>" method="post">

  <p><?php 
    echo $os_c_images->get_title();
    ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php 
    foreach ($os_c_images->get_parameters() as $params) {
        ?>

    <tr>
      <td width="40%"><?php 
        echo '<b>' . $params['key'] . '</b>';
        ?></td>
      <td width="60%"><?php 
        echo $params['field'];
        ?></td>
    </tr>

<?php 
    }
    ?>

  </table>

  <p align="center"><?php 
    echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_execute') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()) . '\';" class="operationButton" />';
    ?></p>

  </form>
</div>

<?php 
} else {
    $os_c_images->activate();
    ?>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>

<?php 
    foreach ($os_c_images->get_header() as $header) {
        echo '      <th>' . $header . '</th>';
    }
    ?>

    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="<?php 
    echo sizeof($os_c_images->get_header());
    ?>">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>

<?php 
    foreach ($os_c_images->get_data() as $data) {
        if (!isset($columns)) {
            $columns = sizeof($data);
        }
        echo '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">';
        for ($i = 0; $i < $columns; $i++) {
            echo '      <td>' . $data[$i] . '</td>';
        }
        echo '    </tr>';
    }
    ?>

  </tbody>
</table>

<?php 
}