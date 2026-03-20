<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Administrators\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Find
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $sql_query = 'select SQL_CALC_FOUND_ROWS * from :table_administrators where (user_name like :user_name) order by user_name';
        if ($data['batch_pageset'] !== -1) {
            $sql_query .= ' limit :batch_pageset, :batch_max_results';
        }
        $sql_query .= '; select found_rows();';
        $Qadmins = $OSCOM_PDO->prepare($sql_query);
        $Qadmins->bind_value(':user_name', '%' . $data['user_name'] . '%');
        if ($data['batch_pageset'] !== -1) {
            $Qadmins->bind_int(':batch_pageset', $OSCOM_PDO->get_batch_from($data['batch_pageset'], $data['batch_max_results']));
            $Qadmins->bind_int(':batch_max_results', $data['batch_max_results']);
        }
        $Qadmins->execute();
        $result['entries'] = $Qadmins->fetch_all();
        $Qadmins->next_rowset();
        $result['total'] = $Qadmins->fetch_column();
        return $result;
    }
}