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
class Update_Definition
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qupdate = $OSCOM_PDO->prepare('update :table_languages_definitions set definition_value = :definition_value where definition_key = :definition_key and languages_id = :languages_id and content_group = :content_group');
        $Qupdate->bind_value(':definition_value', $data['value']);
        $Qupdate->bind_value(':definition_key', $data['key']);
        $Qupdate->bind_int(':languages_id', $data['language_id']);
        $Qupdate->bind_value(':content_group', $data['group']);
        $Qupdate->execute();
        return $Qupdate->row_count() === 1 || !$Qupdate->is_error();
    }
}