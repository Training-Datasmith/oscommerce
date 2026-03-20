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
function osc_cfg_set_order_statuses_pull_down_menu($default, $key = null)
{
    $OSCOM_PDO = Registry::get('PDO');
    $OSCOM_Language = Registry::get('Language');
    $name = empty($key) ? 'configuration_value' : 'configuration[' . $key . ']';
    $statuses_array = [['id' => '0', 'text' => OSCOM::get_def('default_entry')]];
    $Qstatuses = $OSCOM_PDO->prepare('select orders_status_id, orders_status_name from :table_orders_status where language_id = :language_id order by orders_status_name');
    $Qstatuses->bind_int(':language_id', $OSCOM_Language->get_id());
    $Qstatuses->execute();
    while ($Qstatuses->fetch()) {
        $statuses_array[] = ['id' => $Qstatuses->value_int('orders_status_id'), 'text' => $Qstatuses->value('orders_status_name')];
    }
    return HTML::select_menu($name, $statuses_array, $default);
}