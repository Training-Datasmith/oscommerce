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
class Save
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        if (isset($data['id']) && is_numeric($data['id'])) {
            $Qcc = $OSCOM_PDO->prepare('update :table_credit_cards set credit_card_name = :credit_card_name, pattern = :pattern, credit_card_status = :credit_card_status, sort_order = :sort_order where id = :id');
            $Qcc->bind_int(':id', $data['id']);
        } else {
            $Qcc = $OSCOM_PDO->prepare('insert into :table_credit_cards (credit_card_name, pattern, credit_card_status, sort_order) values (:credit_card_name, :pattern, :credit_card_status, :sort_order)');
        }
        $Qcc->bind_value(':credit_card_name', $data['name']);
        $Qcc->bind_value(':pattern', $data['pattern']);
        $Qcc->bind_int(':credit_card_status', $data['status']);
        $Qcc->bind_int(':sort_order', $data['sort_order']);
        $Qcc->execute();
        return $Qcc->row_count() === 1 || !$Qcc->is_error();
    }
}