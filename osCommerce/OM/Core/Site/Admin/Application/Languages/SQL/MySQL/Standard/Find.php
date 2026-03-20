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
class Find
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $sql_query = 'select SQL_CALC_FOUND_ROWS l.*, count(ld.id) as total_definitions from :table_languages l left join :table_languages_definitions ld on (l.languages_id = ld.languages_id) where (l.name like :name or l.code like :code or ld.definition_key like :definition_key or ld.definition_value like :definition_value) group by l.languages_id order by l.name';
        if ($data['batch_pageset'] !== -1) {
            $sql_query .= ' limit :batch_pageset, :batch_max_results';
        }
        $sql_query .= '; select found_rows();';
        $Qlanguages = $OSCOM_PDO->prepare($sql_query);
        $Qlanguages->bind_value(':name', '%' . $data['keywords'] . '%');
        $Qlanguages->bind_value(':code', '%' . $data['keywords'] . '%');
        $Qlanguages->bind_value(':definition_key', '%' . $data['keywords'] . '%');
        $Qlanguages->bind_value(':definition_value', '%' . $data['keywords'] . '%');
        if ($data['batch_pageset'] !== -1) {
            $Qlanguages->bind_int(':batch_pageset', $OSCOM_PDO->get_batch_from($data['batch_pageset'], $data['batch_max_results']));
            $Qlanguages->bind_int(':batch_max_results', $data['batch_max_results']);
        }
        $Qlanguages->execute();
        $result['entries'] = $Qlanguages->fetch_all();
        $Qlanguages->next_rowset();
        $result['total'] = $Qlanguages->fetch_column();
        return $result;
    }
}