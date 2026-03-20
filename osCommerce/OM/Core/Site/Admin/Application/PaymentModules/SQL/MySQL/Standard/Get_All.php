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
class Get_All
{
    public static function execute()
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $Qpm = $OSCOM_PDO->prepare('select code from :table_modules where modules_group = :modules_group order by code');
        $Qpm->bind_value(':modules_group', 'Payment');
        $Qpm->execute();
        $result['entries'] = $Qpm->fetch_all();
        $result['total'] = count($result['entries']);
        return $result;
    }
}