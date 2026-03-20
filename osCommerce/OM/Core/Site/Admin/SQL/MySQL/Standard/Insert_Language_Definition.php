<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Insert_Language_Definition
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        if (isset($data['key']) && isset($data['value'])) {
            $data = [$data];
        }
        $error = false;
        $in_transaction = false;
        if (count($data) > 1) {
            $OSCOM_PDO->begin_transaction();
            $in_transaction = true;
        }
        $Qdef = $OSCOM_PDO->prepare('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
        foreach ($data as $d) {
            $Qdef->bind_int(':languages_id', $d['id']);
            $Qdef->bind_value(':content_group', $d['group']);
            $Qdef->bind_value(':definition_key', $d['key']);
            $Qdef->bind_value(':definition_value', $d['value']);
            $Qdef->execute();
            if ($Qdef->is_error()) {
                if ($in_transaction === true) {
                    $OSCOM_PDO->roll_back();
                }
                $error = true;
                break;
            }
        }
        if ($error === false && $in_transaction === true) {
            $OSCOM_PDO->commit();
        }
        return !$error;
    }
}