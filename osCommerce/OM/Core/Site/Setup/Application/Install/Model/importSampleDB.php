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
class Import_Sample_Db
{
    public static function execute($data)
    {
        Registry::set('PDO', PDO::initialize($data['server'], $data['username'], $data['password'], $data['database'], $data['port'], $data['class']));
        OSCOM::call_db('Setup\Install\ImportSampleSQL', ['table_prefix' => $data['table_prefix']]);
    }
}