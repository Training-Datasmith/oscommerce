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
class Insert_Definition
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qdef = $OSCOM_PDO->prepare('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
        $Qdef->bind_int(':languages_id', $data['language_id']);
        $Qdef->bind_value(':content_group', $data['group']);
        $Qdef->bind_value(':definition_key', $data['key']);
        $Qdef->bind_value(':definition_value', $data['value']);
        $Qdef->execute();
        return $Qdef->row_count() === 1 || !$Qdef->is_error();
    }
}