<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Tax_Classes\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Entry_Save
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        if (isset($data['id']) && is_numeric($data['id'])) {
            $Qrate = $OSCOM_PDO->prepare('update :table_tax_rates set tax_zone_id = :tax_zone_id, tax_priority = :tax_priority, tax_rate = :tax_rate, tax_description = :tax_description, last_modified = now() where tax_rates_id = :tax_rates_id');
            $Qrate->bind_int(':tax_rates_id', $data['id']);
        } else {
            $Qrate = $OSCOM_PDO->prepare('insert into :table_tax_rates (tax_zone_id, tax_class_id, tax_priority, tax_rate, tax_description, date_added) values (:tax_zone_id, :tax_class_id, :tax_priority, :tax_rate, :tax_description, now())');
            $Qrate->bind_int(':tax_class_id', $data['tax_class_id']);
        }
        $Qrate->bind_int(':tax_zone_id', $data['zone_id']);
        $Qrate->bind_int(':tax_priority', $data['priority']);
        $Qrate->bind_value(':tax_rate', $data['rate']);
        $Qrate->bind_value(':tax_description', $data['description']);
        $Qrate->execute();
        return $Qrate->row_count() === 1 || !$Qrate->is_error();
    }
}