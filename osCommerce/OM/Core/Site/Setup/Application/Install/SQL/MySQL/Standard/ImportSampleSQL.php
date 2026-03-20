<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Setup\Application\Install\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
class Import_Sample_Sql
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $sql_file = OSCOM::BASE_DIRECTORY . 'Core/Site/Setup/sql/oscommerce_sample_data.sql';
        return $OSCOM_PDO->import_sql($sql_file, $data['table_prefix']);
    }
}