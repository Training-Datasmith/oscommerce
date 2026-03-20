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
class Entry_Get
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        if (isset($data['key'])) {
            $Qcfg = $OSCOM_PDO->prepare('select * from :table_configuration where configuration_key = :configuration_key');
            $Qcfg->bind_value(':configuration_key', $data['key']);
        } else {
            $Qcfg = $OSCOM_PDO->prepare('select * from :table_configuration where configuration_id = :configuration_id');
            $Qcfg->bind_int(':configuration_id', $data['id']);
        }
        $Qcfg->execute();
        return $Qcfg->fetch();
    }
}