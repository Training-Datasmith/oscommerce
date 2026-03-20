<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Configuration\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Entry_Save
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qupdate = $OSCOM_PDO->prepare('update :table_configuration set configuration_value = :configuration_value, last_modified = now() where configuration_key = :configuration_key');
        $Qupdate->bind_value(':configuration_value', $data['value']);
        $Qupdate->bind_value(':configuration_key', $data['key']);
        $Qupdate->execute();
        return $Qupdate->row_count() === 1 || !$Qupdate->is_error();
    }
}