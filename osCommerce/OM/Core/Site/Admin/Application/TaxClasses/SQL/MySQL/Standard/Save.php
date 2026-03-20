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
class Save
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        if (isset($data['id']) && is_numeric($data['id'])) {
            $Qclass = $OSCOM_PDO->prepare('update :table_tax_class set tax_class_title = :tax_class_title, tax_class_description = :tax_class_description, last_modified = now() where tax_class_id = :tax_class_id');
            $Qclass->bind_int(':tax_class_id', $data['id']);
        } else {
            $Qclass = $OSCOM_PDO->prepare('insert into :table_tax_class (tax_class_title, tax_class_description, date_added) values (:tax_class_title, :tax_class_description, now())');
        }
        $Qclass->bind_value(':tax_class_title', $data['title']);
        $Qclass->bind_value(':tax_class_description', $data['description']);
        $Qclass->execute();
        return $Qclass->row_count() === 1 || !$Qclass->is_error();
    }
}