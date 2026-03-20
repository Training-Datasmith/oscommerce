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
class Delete_Configuration_Parameters
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        if (!is_array($data)) {
            $data = [$data];
        }
        $error = false;
        $in_transaction = false;
        if (count($data) > 1) {
            $OSCOM_PDO->begin_transaction();
            $in_transaction = true;
        }
        $Qcfg = $OSCOM_PDO->prepare('delete from :table_configuration where configuration_key = :configuration_key');
        foreach ($data as $key) {
            $Qcfg->bind_value(':configuration_key', $key);
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