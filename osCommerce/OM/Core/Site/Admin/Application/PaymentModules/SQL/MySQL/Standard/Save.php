<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Payment_Modules\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Save
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $error = false;
        $OSCOM_PDO->begin_transaction();
        foreach ($data['configuration'] as $key => $value) {
            $Qupdate = $OSCOM_PDO->prepare('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
            $Qupdate->bind_value(':configuration_value', is_array($data['configuration'][$key]) ? implode(',', $data['configuration'][$key]) : $value);
            $Qupdate->bind_value(':configuration_key', $key);
            $Qupdate->execute();
            if ($Qupdate->is_error()) {
                $error = true;
                break;
            }
        }
        if ($error === false) {
            $OSCOM_PDO->commit();
            return true;
        }
        $OSCOM_PDO->roll_back();
        return false;
    }
}