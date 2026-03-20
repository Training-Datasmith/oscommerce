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
class Delete
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qcheck = $OSCOM_PDO->prepare('select code from :table_currencies where currencies_id = :currencies_id');
        $Qcheck->bind_int(':currencies_id', $data['id']);
        $Qcheck->execute();
        if ($Qcheck->value('code') != DEFAULT_CURRENCY) {
            $Qdelete = $OSCOM_PDO->prepare('delete from :table_currencies where currencies_id = :currencies_id');
            $Qdelete->bind_int(':currencies_id', $data['id']);
            $Qdelete->execute();
            return $Qdelete->row_count() === 1;
        }
        return false;
    }
}