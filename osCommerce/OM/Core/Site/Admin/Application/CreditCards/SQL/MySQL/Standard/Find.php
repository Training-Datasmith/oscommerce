<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Credit_Cards\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Find
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $sql_query = 'select SQL_CALC_FOUND_ROWS * from :table_credit_cards where (credit_card_name like :credit_card_name) order by credit_card_name';
        if ($data['batch_pageset'] !== -1) {
            $sql_query .= ' limit :batch_pageset, :batch_max_results';
        }
        $sql_query .= '; select found_rows();';
        $Qcc = $OSCOM_PDO->prepare($sql_query);
        $Qcc->bind_value(':credit_card_name', '%' . $data['keywords'] . '%');
        if ($data['batch_pageset'] !== -1) {
            $Qcc->bind_int(':batch_pageset', $OSCOM_PDO->get_batch_from($data['batch_pageset'], $data['batch_max_results']));
            $Qcc->bind_int(':batch_max_results', $data['batch_max_results']);
        }
        $Qcc->execute();
        $result['entries'] = $Qcc->fetch_all();
        $Qcc->next_rowset();
        $result['total'] = $Qcc->fetch_column();
        return $result;
    }
}