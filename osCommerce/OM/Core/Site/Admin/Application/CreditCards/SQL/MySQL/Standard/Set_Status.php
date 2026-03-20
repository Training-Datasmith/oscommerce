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
class Set_Status
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qcc = $OSCOM_PDO->prepare('update :table_credit_cards set credit_card_status = :credit_card_status where id = :id');
        $Qcc->bind_int(':credit_card_status', $data['status'] === true ? 1 : 0);
        $Qcc->bind_int(':id', $data['id']);
        $Qcc->execute();
        return $Qcc->row_count() === 1 || !$Qcc->is_error();
    }
}