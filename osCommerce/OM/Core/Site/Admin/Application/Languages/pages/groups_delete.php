<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\Object_Info;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Languages\Languages;
$oscom_object_info = new Object_Info(Languages::get_group($_GET['group']));
?>

<h1><?php 
echo $OSCOM_Template->get_icon(32) . HTML::link(OSCOM::get_link(), $OSCOM_Template->get_page_title());
?></h1>

<?php 
if ($oscom_message_stack->exists()) {
    echo $oscom_message_stack->get();
}
?>

<div class="infoBox">
  <h3><?php 
echo HTML::icon('trash.png') . ' ' . HTML::output_protected($_GET['group']);
?></h3>

  <form name="gDelete" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'DeleteGroup&Process&id=' . $_GET['id'] . '&group=' . $_GET['group']);
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_delete_definition_group');
?></p>

  <p><?php 
echo '<b>' . HTML::output_protected($_GET['group']) . '</b>';
?></p>

  <p>

<?php 
foreach ($oscom_object_info->get('entries') as $l) {
    echo Languages::get($l['languages_id'], 'name') . ': ' . (int) $l['total_entries'] . '<br />';
}
?>

  </p>

  <p><?php 
echo HTML::button(['priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::get_def('button_delete')]) . ' ' . HTML::button(['href' => OSCOM::get_link(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]);
?></p>

  </form>
</div>
