<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Countries\SQL\Microsoft\Sql_Server;

use Os_Commerce\OM\Core\Registry;
class Get
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Q = $OSCOM_PDO->prepare('EXEC CountriesGet :countries_id');
        $Q->bind_int(':countries_id', $data['id']);
        $Q->execute();
        $result_1 = $Q->to_array();
        $Q->next_result_set();
        $result_2 = $Q->to_array();
        return array_merge($result_1, $result_2);
    }
}