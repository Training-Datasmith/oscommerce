<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Countries\SQL\My_Sql\V5;

use Os_Commerce\OM\Core\Registry;
class Get_All
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $result = [];
        $Qcountries = $OSCOM_PDO->prepare('CALL CountriesGetAll(:batch_pageset, :batch_max_results)');
        if ($data['batch_pageset'] !== -1) {
            $Qcountries->bind_int(':batch_pageset', $OSCOM_PDO->get_batch_from($data['batch_pageset'], $data['batch_max_results']));
            $Qcountries->bind_int(':batch_max_results', $data['batch_max_results']);
        } else {
            $Qcountries->bind_null(':batch_pageset');
            $Qcountries->bind_null(':batch_max_results');
        }
        $Qcountries->execute();
        $result['entries'] = $Qcountries->fetch_all();
        $Qcountries->next_rowset();
        $result['total'] = $Qcountries->fetch_column();
        return $result;
    }
}