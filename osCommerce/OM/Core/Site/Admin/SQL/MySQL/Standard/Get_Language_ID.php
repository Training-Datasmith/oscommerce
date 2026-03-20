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
class Get_Language_Id
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qlanguage = $OSCOM_PDO->prepare('select languages_id from :table_languages where code = :code');
        $Qlanguage->bind_value(':code', $data['code']);
        $Qlanguage->execute();
        return $Qlanguage->fetch();
    }
}