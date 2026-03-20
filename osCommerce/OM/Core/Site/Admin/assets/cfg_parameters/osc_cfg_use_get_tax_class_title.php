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
function osc_cfg_use_get_tax_class_title($id)
{
    $OSCOM_PDO = Registry::get('PDO');
    $OSCOM_Language = Registry::get('Language');
    if ($id < 1) {
        return OSCOM::get_def('parameter_none');
    }
    $Qclass = $OSCOM_PDO->prepare('select tax_class_title from :table_tax_class where tax_class_id = :tax_class_id');
    $Qclass->bind_int(':tax_class_id', $id);
    $Qclass->execute();
    return $Qclass->value('tax_class_title');
}