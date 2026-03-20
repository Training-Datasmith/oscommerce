<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Server_Info\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Get_Uptime
{
    public static function execute()
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = $OSCOM_PDO->query('show status like "Uptime"')->fetch();
        return intval($result['Value'] / 3600) . ':' . str_pad(intval($result['Value'] / 60 % 60), 2, '0', STR_PAD_LEFT);
    }
}