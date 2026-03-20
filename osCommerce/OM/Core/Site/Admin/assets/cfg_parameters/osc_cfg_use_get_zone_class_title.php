<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
function osc_cfg_use_get_zone_class_title($id)
{
    $OSCOM_PDO = Registry::get('PDO');
    $OSCOM_Language = Registry::get('Language');
    if ($id == '0') {
        return OSCOM::get_def('parameter_none');
    }
    $Qclass = $OSCOM_PDO->prepare('select geo_zone_name from :table_geo_zones where geo_zone_id = :geo_zone_id');
    $Qclass->bind_int(':geo_zone_id', $id);
    $Qclass->execute();
    return $Qclass->value('geo_zone_name');
}