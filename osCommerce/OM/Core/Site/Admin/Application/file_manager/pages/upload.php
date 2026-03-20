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
?>

<h1><?php 
echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()), $os_c_template->get_page_title());
?></h1>

<?php 
if ($os_c_message_stack->size($os_c_template->get_module()) > 0) {
    echo $os_c_message_stack->get($os_c_template->get_module());
}
?>

<div class="infoBoxHeading"><?php 
echo osc_icon('new.png') . ' ' . $os_c_language->get('action_heading_upload_file');
?></div>
<div class="infoBoxContent">

<?php 
if (is_writeable($_SESSION['fm_directory'])) {
    ?>

  <form name="fmUpload" action="<?php 
    echo osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module() . '&action=upload');
    ?>" method="post" enctype="multipart/form-data">

  <p><?php 
    echo $os_c_language->get('introduction_upload_file');
    ?></p>

  <p><?php 
    echo '<b>' . osc_output_string_protected($_SESSION['fm_directory']) . '</b>';
    ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php 
    for ($i = 0; $i < 10; $i++) {
        ?>

    <tr>
      <td width="40%"><?php 
        echo '<b>' . $os_c_language->get('field_file') . '</b>';
        ?></td>
      <td width="60%"><?php 
        echo osc_draw_file_field('file_' . $i, true);
        ?></td>
    </tr>

<?php 
    }
    ?>

  </table>

  <p align="center"><?php 
    echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $os_c_language->get('button_upload') . '" class="operationButton" /> <input type="button" value="' . $os_c_language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()) . '\';" class="operationButton" />';
    ?></p>

  </form>

<?php 
} else {
    ?>

  <p><?php 
    echo sprintf($os_c_language->get('upload_error_directory_not_writable'), $_SESSION['fm_directory']);
    ?></p>

  <p align="center"><?php 
    echo '<input type="button" value="' . $os_c_language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $os_c_template->get_module()) . '\';" class="operationButton" />';
    ?></p>

<?php 
}
?>

</div>
