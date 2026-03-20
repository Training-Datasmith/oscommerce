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
class Update_Rate
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qupdate = $OSCOM_PDO->prepare('update :table_currencies set value = :value, last_updated = now() where currencies_id = :currencies_id');
        $Qupdate->bind_value(':value', $data['rate']);
        $Qupdate->bind_int(':currencies_id', $data['id']);
        $Qupdate->execute();
        return $Qupdate->row_count() === 1 || !$Qupdate->is_error();
    }
}