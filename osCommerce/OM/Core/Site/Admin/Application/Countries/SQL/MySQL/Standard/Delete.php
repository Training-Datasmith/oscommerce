<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Countries\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Delete
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qcountry = $OSCOM_PDO->prepare('delete from :table_countries where countries_id = :countries_id');
        $Qcountry->bind_int(':countries_id', $data['id']);
        $Qcountry->execute();
        return $Qcountry->row_count() === 1;
    }
}