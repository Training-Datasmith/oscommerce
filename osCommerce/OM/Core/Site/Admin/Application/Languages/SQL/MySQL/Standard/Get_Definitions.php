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
class Get_Definitions
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $sql_query = 'select * from :table_languages_definitions where languages_id = :languages_id and';
        if (is_array($data['group'])) {
            $sql_query .= ' content_group in ("' . implode('", "', $data['group']) . '")';
        } else {
            $sql_query .= ' content_group = :content_group';
        }
        $sql_query .= ' order by content_group, definition_key';
        $Qdefs = $OSCOM_PDO->prepare($sql_query);
        if (!is_array($data['group'])) {
            $Qdefs->bind_value(':content_group', $data['group']);
        }
        $Qdefs->bind_int(':languages_id', $data['id']);
        $Qdefs->execute();
        $result['entries'] = $Qdefs->fetch_all();
        $result['total'] = count($result['entries']);
        return $result;
    }
}