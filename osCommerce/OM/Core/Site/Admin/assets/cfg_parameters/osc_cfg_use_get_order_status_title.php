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
function osc_cfg_use_get_order_status_title($id)
{
    $OSCOM_PDO = Registry::get('PDO');
    $OSCOM_Language = Registry::get('Language');
    if ($id < 1) {
        return OSCOM::get_def('default_entry');
    }
    $Qstatus = $OSCOM_PDO->prepare('select orders_status_name from :table_orders_status where orders_status_id = :orders_status_id and language_id = :language_id');
    $Qstatus->bind_int(':orders_status_id', $id);
    $Qstatus->bind_int(':language_id', $OSCOM_Language->get_id());
    $Qstatus->execute();
    return $Qstatus->value('orders_status_name');
}