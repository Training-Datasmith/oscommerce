<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Dashboard\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Get_Shortcuts
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qshortcuts = $OSCOM_PDO->prepare('select module, last_viewed from :table_administrator_shortcuts where administrators_id = :administrators_id');
        $Qshortcuts->bind_int(':administrators_id', $data['admin_id']);
        $Qshortcuts->execute();
        return $Qshortcuts->fetch_all();
    }
}