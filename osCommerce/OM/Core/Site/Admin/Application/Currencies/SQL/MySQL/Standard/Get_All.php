<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Currencies\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Get_All
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $sql_query = 'select SQL_CALC_FOUND_ROWS * from :table_currencies order by title';
        if ($data['batch_pageset'] !== -1) {
            $sql_query .= ' limit :batch_pageset, :batch_max_results';
        }
        $sql_query .= '; select found_rows();';
        $Qcurrencies = $OSCOM_PDO->prepare($sql_query);
        if ($data['batch_pageset'] !== -1) {
            $Qcurrencies->bind_int(':batch_pageset', $OSCOM_PDO->get_batch_from($data['batch_pageset'], $data['batch_max_results']));
            $Qcurrencies->bind_int(':batch_max_results', $data['batch_max_results']);
        }
        $Qcurrencies->execute();
        $result['entries'] = $Qcurrencies->fetch_all();
        $Qcurrencies->next_rowset();
        $result['total'] = $Qcurrencies->fetch_column();
        return $result;
    }
}