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
class Entry_Delete
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qrate = $OSCOM_PDO->prepare('delete from :table_tax_rates where tax_rates_id = :tax_rates_id');
        $Qrate->bind_int(':tax_rates_id', $data['id']);
        $Qrate->execute();
        return $Qrate->row_count() === 1;
    }
}