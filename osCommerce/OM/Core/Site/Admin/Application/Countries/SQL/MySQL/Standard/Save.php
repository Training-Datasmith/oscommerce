<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Countries\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Save
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        if (isset($data['id']) && is_numeric($data['id'])) {
            $Qcountry = $OSCOM_PDO->prepare('update :table_countries set countries_name = :countries_name, countries_iso_code_2 = :countries_iso_code_2, countries_iso_code_3 = :countries_iso_code_3, address_format = :address_format where countries_id = :countries_id');
            $Qcountry->bind_int(':countries_id', $data['id']);
        } else {
            $Qcountry = $OSCOM_PDO->prepare('insert into :table_countries (countries_name, countries_iso_code_2, countries_iso_code_3, address_format) values (:countries_name, :countries_iso_code_2, :countries_iso_code_3, :address_format)');
        }
        $Qcountry->bind_value(':countries_name', $data['name']);
        $Qcountry->bind_value(':countries_iso_code_2', $data['iso_code_2']);
        $Qcountry->bind_value(':countries_iso_code_3', $data['iso_code_3']);
        $Qcountry->bind_value(':address_format', $data['address_format']);
        $Qcountry->execute();
        return $Qcountry->row_count() === 1 || !$Qcountry->is_error();
    }
}