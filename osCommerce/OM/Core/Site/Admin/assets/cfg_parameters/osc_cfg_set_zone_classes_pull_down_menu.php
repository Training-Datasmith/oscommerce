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
function osc_cfg_set_zone_classes_pull_down_menu($default, $key = null)
{
    $OSCOM_PDO = Registry::get('PDO');
    $name = empty($key) ? 'configuration_value' : 'configuration[' . $key . ']';
    $zone_class_array = [['id' => '0', 'text' => OSCOM::get_def('parameter_none')]];
    $Qzones = $OSCOM_PDO->query('select geo_zone_id, geo_zone_name from :table_geo_zones order by geo_zone_name');
    $Qzones->execute();
    while ($Qzones->fetch()) {
        $zone_class_array[] = ['id' => $Qzones->value_int('geo_zone_id'), 'text' => $Qzones->value('geo_zone_name')];
    }
    return HTML::select_menu($name, $zone_class_array, $default);
}