<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Currencies\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Save
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_PDO->begin_transaction();
        if (is_numeric($data['id'])) {
            $Qcurrency = $OSCOM_PDO->prepare('update :table_currencies set title = :title, code = :code, symbol_left = :symbol_left, symbol_right = :symbol_right, decimal_places = :decimal_places, value = :value where currencies_id = :currencies_id');
            $Qcurrency->bind_int(':currencies_id', $data['id']);
        } else {
            $Qcurrency = $OSCOM_PDO->prepare('insert into :table_currencies (title, code, symbol_left, symbol_right, decimal_places, value) values (:title, :code, :symbol_left, :symbol_right, :decimal_places, :value)');
        }
        $Qcurrency->bind_value(':title', $data['title']);
        $Qcurrency->bind_value(':code', $data['code']);
        $Qcurrency->bind_value(':symbol_left', $data['symbol_left']);
        $Qcurrency->bind_value(':symbol_right', $data['symbol_right']);
        $Qcurrency->bind_int(':decimal_places', $data['decimal_places']);
        $Qcurrency->bind_value(':value', $data['value']);
        $Qcurrency->execute();
        if (!$Qcurrency->is_error()) {
            if ($data['set_default'] === true) {
                $Qupdate = $OSCOM_PDO->prepare('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
                $Qupdate->bind_value(':configuration_value', $data['code']);
                $Qupdate->bind_value(':configuration_key', 'DEFAULT_CURRENCY');
                $Qupdate->execute();
            }
            $OSCOM_PDO->commit();
            return true;
        }
        $OSCOM_PDO->roll_back();
        return false;
    }
}