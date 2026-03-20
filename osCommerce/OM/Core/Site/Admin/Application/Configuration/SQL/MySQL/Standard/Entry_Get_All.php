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
use Os_Commerce\OM\Core\Site\Admin\Application\Configuration\Configuration;
class Entry_Get_All
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = ['entries' => []];
        $Qcfg = $OSCOM_PDO->prepare('select * from :table_configuration where configuration_group_id = :configuration_group_id order by sort_order, configuration_title');
        $Qcfg->bind_int(':configuration_group_id', $data['group_id']);
        $Qcfg->execute();
        while ($row = $Qcfg->fetch()) {
            $result['entries'][] = $row;
            if (!empty($row['use_function'])) {
                $result['entries'][count($result['entries']) - 1]['configuration_value'] = Configuration::call_user_func($row['use_function'], $row['configuration_value']);
            }
        }
        $result['total'] = count($result['entries']);
        return $result;
    }
}