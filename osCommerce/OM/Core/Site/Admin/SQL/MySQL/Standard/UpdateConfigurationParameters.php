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
class Update_Configuration_Parameters
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
        $Qcfg = $OSCOM_PDO->prepare('update :table_configuration set configuration_value = :configuration_value, last_modified = now() where configuration_key = :configuration_key');
        foreach ($data as $d) {
            $Qcfg->bind_value(':configuration_value', $d['value']);
            $Qcfg->bind_value(':configuration_key', $d['key']);
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