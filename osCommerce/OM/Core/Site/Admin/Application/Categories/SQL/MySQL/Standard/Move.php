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
class Move
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $error = false;
        $Qupdate = $OSCOM_PDO->prepare('update :table_categories set parent_id = :parent_id, last_modified = now() where categories_id = :categories_id');
        if ($data['parent_id'] > 0) {
            $Qupdate->bind_int(':parent_id', $data['parent_id']);
        } else {
            $Qupdate->bind_null(':parent_id');
        }
        $Qupdate->bind_int(':categories_id', $data['id']);
        $Qupdate->execute();
        return $Qupdate->row_count() === 1 || !$Qupdate->is_error();
    }
}