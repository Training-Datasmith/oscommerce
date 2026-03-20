<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Insert_Configuration_Parameters
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        if (isset($data['key']) && isset($data['value'])) {
            $data = [$data];
        }
        $error = false;
        $in_transaction = false;
        if (count($data) > 1) {
            $OSCOM_PDO->begin_transaction();
            $in_transaction = true;
        }
        $Qcfg = $OSCOM_PDO->prepare('insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values (:configuration_title, :configuration_key, :configuration_value, :configuration_description, :configuration_group_id, :sort_order, :use_function, :set_function, now())');
        foreach ($data as $d) {
            if (!isset($d['sort_order'])) {
                $d['sort_order'] = '0';
            }
            if (!isset($d['use_function'])) {
                $d['use_function'] = '';
            }
            if (!isset($d['set_function'])) {
                $d['set_function'] = '';
            }
            $Qcfg->bind_value(':configuration_title', $d['title']);
            $Qcfg->bind_value(':configuration_key', $d['key']);
            $Qcfg->bind_value(':configuration_value', $d['value']);
            $Qcfg->bind_value(':configuration_description', $d['description']);
            $Qcfg->bind_int(':configuration_group_id', $d['group_id']);
            $Qcfg->bind_int(':sort_order', $d['sort_order']);
            $Qcfg->bind_value(':use_function', $d['use_function']);
            $Qcfg->bind_value(':set_function', $d['set_function']);
            $Qcfg->execute();
            if ($Qcfg->is_error()) {
                if ($in_transaction === true) {
                    $OSCOM_PDO->roll_back();
                }
                $error = true;
                break;
            }
        }
        if ($error === false && $in_transaction === true) {
            $OSCOM_PDO->commit();
        }
        return !$error;
    }
}