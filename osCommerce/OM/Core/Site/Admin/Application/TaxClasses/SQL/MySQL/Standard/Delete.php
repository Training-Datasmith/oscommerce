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
class Delete
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qclass = $OSCOM_PDO->prepare('delete from :table_tax_class where tax_class_id = :tax_class_id');
        $Qclass->bind_int(':tax_class_id', $data['id']);
        $Qclass->execute();
        return $Qclass->row_count() === 1;
    }
}