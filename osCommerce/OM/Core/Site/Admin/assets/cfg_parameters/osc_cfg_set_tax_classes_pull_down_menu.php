<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
function osc_cfg_set_tax_classes_pull_down_menu($default, $key = null)
{
    $OSCOM_Language = Registry::get('Language');
    $OSCOM_PDO = Registry::get('PDO');
    $name = empty($key) ? 'configuration_value' : 'configuration[' . $key . ']';
    $tax_class_array = [['id' => '0', 'text' => OSCOM::get_def('parameter_none')]];
    $Qclasses = $OSCOM_PDO->query('select tax_class_id, tax_class_title from :table_tax_class order by tax_class_title');
    $Qclasses->execute();
    while ($Qclasses->fetch()) {
        $tax_class_array[] = ['id' => $Qclasses->value_int('tax_class_id'), 'text' => $Qclasses->value('tax_class_title')];
    }
    return HTML::select_menu($name, $tax_class_array, $default);
}