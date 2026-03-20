<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Categories\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
/**
 * @since v3.0.2
 */
class Save_Sort_Order
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $error = false;
        $OSCOM_PDO->begin_transaction();
        foreach ($data as $c) {
            $Qcategory = $OSCOM_PDO->prepare('update :table_categories set sort_order = :sort_order, last_modified = now() where categories_id = :categories_id');
            $Qcategory->bind_int(':sort_order', $c['sort_order']);
            $Qcategory->bind_int(':categories_id', $c['id']);
            $Qcategory->execute();
            if ($Qcategory->is_error()) {
                $error = true;
                break;
            }
        }
        if ($error === false) {
            $OSCOM_PDO->commit();
            return true;
        }
        $OSCOM_PDO->roll_back();
        return false;
    }
}