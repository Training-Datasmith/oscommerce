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
class Find_Definitions
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $Qdefs = $OSCOM_PDO->prepare('select * from :table_languages_definitions where languages_id = :languages_id and content_group = :content_group and (definition_key like :definition_key or definition_value like :definition_value) order by definition_key');
        $Qdefs->bind_int(':languages_id', $data['id']);
        $Qdefs->bind_value(':content_group', $data['group']);
        $Qdefs->bind_value(':definition_key', '%' . $data['keywords'] . '%');
        $Qdefs->bind_value(':definition_value', '%' . $data['keywords'] . '%');
        $Qdefs->execute();
        $result['entries'] = $Qdefs->fetch_all();
        $result['total'] = count($result['entries']);
        return $result;
    }
}