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
class Get_Version
{
    public static function execute()
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = $OSCOM_PDO->query('select version() as version')->fetch();
        return 'MySQL v' . $result['version'];
    }
}