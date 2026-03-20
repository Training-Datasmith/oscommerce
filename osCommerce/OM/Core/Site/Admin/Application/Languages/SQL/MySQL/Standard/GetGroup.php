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
class Get_Group
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $Qgroup = $OSCOM_PDO->prepare('select languages_id, count(*) as total_entries from :table_languages_definitions where content_group = :content_group group by languages_id');
        $Qgroup->bind_value(':content_group', $data['group']);
        $Qgroup->execute();
        $result['entries'] = $Qgroup->fetch_all();
        $result['total'] = count($result['entries']);
        return $result;
    }
}