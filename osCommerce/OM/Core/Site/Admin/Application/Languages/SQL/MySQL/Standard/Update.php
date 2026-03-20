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
class Update
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_PDO->begin_transaction();
        $Qlanguage = $OSCOM_PDO->prepare('update :table_languages set name = :name, code = :code, locale = :locale, charset = :charset, date_format_short = :date_format_short, date_format_long = :date_format_long, time_format = :time_format, text_direction = :text_direction, currencies_id = :currencies_id, numeric_separator_decimal = :numeric_separator_decimal, numeric_separator_thousands = :numeric_separator_thousands, parent_id = :parent_id, sort_order = :sort_order where languages_id = :languages_id');
        $Qlanguage->bind_value(':name', $data['name']);
        $Qlanguage->bind_value(':code', $data['code']);
        $Qlanguage->bind_value(':locale', $data['locale']);
        $Qlanguage->bind_value(':charset', $data['charset']);
        $Qlanguage->bind_value(':date_format_short', $data['date_format_short']);
        $Qlanguage->bind_value(':date_format_long', $data['date_format_long']);
        $Qlanguage->bind_value(':time_format', $data['time_format']);
        $Qlanguage->bind_value(':text_direction', $data['text_direction']);
        $Qlanguage->bind_int(':currencies_id', $data['currencies_id']);
        $Qlanguage->bind_value(':numeric_separator_decimal', $data['numeric_separator_decimal']);
        $Qlanguage->bind_value(':numeric_separator_thousands', $data['numeric_separator_thousands']);
        $Qlanguage->bind_int(':parent_id', $data['parent_id']);
        $Qlanguage->bind_int(':sort_order', $data['sort_order']);
        $Qlanguage->bind_int(':languages_id', $data['id']);
        $Qlanguage->execute();
        if (!$Qlanguage->is_error()) {
            if ($data['set_default'] === true) {
                $Qupdate = $OSCOM_PDO->prepare('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
                $Qupdate->bind_value(':configuration_value', $data['code']);
                $Qupdate->bind_value(':configuration_key', 'DEFAULT_LANGUAGE');
                $Qupdate->execute();
            }
            $OSCOM_PDO->commit();
            return true;
        }
        $OSCOM_PDO->roll_back();
        return false;
    }
}