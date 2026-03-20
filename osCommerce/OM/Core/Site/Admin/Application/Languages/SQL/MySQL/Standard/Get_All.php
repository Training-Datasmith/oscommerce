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
class Get_All
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $sql_query = 'select SQL_CALC_FOUND_ROWS l.*, count(ld.id) as total_definitions from :table_languages l left join :table_languages_definitions ld on (l.languages_id = ld.languages_id) group by l.languages_id order by l.sort_order, l.name';
        if ($data['batch_pageset'] !== -1) {
            $sql_query .= ' limit :batch_pageset, :batch_max_results';
        }
        $sql_query .= '; select found_rows();';
        $Qlanguages = $OSCOM_PDO->prepare($sql_query);
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