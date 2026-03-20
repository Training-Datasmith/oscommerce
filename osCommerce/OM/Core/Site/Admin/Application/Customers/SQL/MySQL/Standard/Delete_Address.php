<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Customers\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
/**
 * @since v3.0.2
 */
class Delete_Address
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qdelete = $OSCOM_PDO->prepare('delete from :table_address_book where address_book_id = :address_book_id and customers_id = :customers_id');
        $Qdelete->bind_int(':address_book_id', $data['id']);
        $Qdelete->bind_int(':customers_id', $data['customer_id']);
        $Qdelete->execute();
        return $Qdelete->row_count() === 1;
    }
}