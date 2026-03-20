<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Session\Database\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Delete_Expired
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qsession = $OSCOM_PDO->prepare('delete from :table_sessions where expiry < :expiry');
        $Qsession->bind_int(':expiry', $data['expiry']);
        $Qsession->execute();
        return $Qsession->row_count() > 0;
    }
}