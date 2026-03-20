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
class Get
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qcc = $OSCOM_PDO->prepare('select * from :table_credit_cards where id = :id');
        $Qcc->bind_int(':id', $data['id']);
        $Qcc->execute();
        return $Qcc->fetch();
    }
}