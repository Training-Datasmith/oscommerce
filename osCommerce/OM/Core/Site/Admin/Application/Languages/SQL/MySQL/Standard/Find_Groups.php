<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Languages\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Find_Groups
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $Qgroups = $OSCOM_PDO->prepare('select distinct content_group, count(*) as total_entries from :table_languages_definitions where languages_id = :languages_id and (definition_key like :definition_key or definition_value like :definition_value) group by content_group order by content_group');
        $Qgroups->bind_int(':languages_id', $data['id']);
        $Qgroups->bind_value(':definition_key', '%' . $data['keywords'] . '%');
        $Qgroups->bind_value(':definition_value', '%' . $data['keywords'] . '%');
        $Qgroups->execute();
        $result['entries'] = $Qgroups->fetch_all();
        $result['total'] = count($result['entries']);
        return $result;
    }
}