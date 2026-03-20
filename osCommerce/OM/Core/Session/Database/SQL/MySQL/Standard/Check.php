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
/**
 * @since v3.0.2
 */
class Check
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qsession = $OSCOM_PDO->prepare('select 1 from :table_sessions where id = :id');
        $Qsession->bind_value(':id', $data['id']);
        $Qsession->execute();
        return $Qsession->fetch() !== false;
    }
}