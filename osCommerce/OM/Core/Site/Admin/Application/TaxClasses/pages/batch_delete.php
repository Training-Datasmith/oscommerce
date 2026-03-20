<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Tax_Classes\Tax_Classes;
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
echo HTML::icon('trash.png') . ' ' . OSCOM::get_def('action_heading_batch_delete_tax_classes');
?></h3>

  <form name="tcDeleteBatch" class="dataForm" action="<?php 
echo OSCOM::get_link(null, null, 'BatchDelete&Process');
?>" method="post">

  <p><?php 
echo OSCOM::get_def('introduction_batch_delete_tax_classes');
?></p>

<?php 
$check_tax_classes_flag = [];
$Qclasses = $OSCOM_PDO->query('select tax_class_id, tax_class_title from :table_tax_class where tax_class_id in ("' . implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))) . '") order by tax_class_title');
$Qclasses->execute();
$names_string = '';
while ($Qclasses->fetch()) {
    if (Tax_Classes::has_products($Qclasses->value_int('tax_class_id'))) {
        $check_tax_classes_flag[] = $Qclasses->value('tax_class_title');
    }
    $names_string .= HTML::hidden_field('batch[]', $Qclasses->value_int('tax_class_id')) . '<b>' . $Qclasses->value('tax_class_title') . ' (' . sprintf(OSCOM::get_def('total_entries'), Tax_Classes::get_number_of_tax_rates($Qclasses->value_int('tax_class_id'))) . ')</b>, ';
}
if (!empty($names_string)) {
    $names_string = substr($names_string, 0, -2);
}
echo '<p>' . $names_string . '</p>';
if (empty($check_tax_classes_flag)) {
    echo '<p>' . HTML::button(['priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::get_def('button_delete')]) . ' ' . HTML::button(['href' => OSCOM::get_link(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::get_def('button_cancel')]) . '</p>';
} else {
    echo '<p><b>' . OSCOM::get_def('batch_delete_warning_tax_class_in_use') . '</b></p>' . '<p>' . implode(', ', $check_tax_classes_flag) . '</p>';
    echo '<p>' . HTML::button(['href' => OSCOM::get_link(), 'icon' => 'triangle-1-w', 'title' => OSCOM::get_def('button_back')]) . '</p>';
}
?>

  </form>
</div>
