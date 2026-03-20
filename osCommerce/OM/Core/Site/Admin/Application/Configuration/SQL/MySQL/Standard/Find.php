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
class Find
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $Qgroups = $OSCOM_PDO->prepare('select distinct cg.configuration_group_id, cg.configuration_group_title, count(c.configuration_id) as total_entries from :table_configuration_group cg, :table_configuration c where (c.configuration_key like :configuration_key or c.configuration_value like :configuration_value) and c.configuration_group_id = cg.configuration_group_id and cg.visible = 1 group by cg.configuration_group_id order by cg.sort_order, cg.configuration_group_title');
        $Qgroups->bind_value(':configuration_key', '%' . $data['search'] . '%');
        $Qgroups->bind_value(':configuration_value', '%' . $data['search'] . '%');
        $Qgroups->execute();
        $result['entries'] = $Qgroups->fetch_all();
        $result['total'] = count($result['entries']);
        return $result;
    }
}