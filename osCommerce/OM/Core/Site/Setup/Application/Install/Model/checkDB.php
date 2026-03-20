<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Setup\Application\Install\Model;

use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\PDO;
use Os_Commerce\OM\Core\Registry;
class Check_Db
{
    public static function execute($data)
    {
        if ($OSCOM_PDO = PDO::initialize($data['server'], $data['username'], $data['password'], null, $data['port'], $data['class'])) {
            Registry::set('PDO', $OSCOM_PDO);
            OSCOM::call_db('Setup\Install\CreateDB', ['database' => $data['database']]);
        }
        return PDO::initialize($data['server'], $data['username'], $data['password'], $data['database'], $data['port'], $data['class']);
    }
}