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
class Update_App_Last_Opened
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qreset = $OSCOM_PDO->prepare('update :table_administrator_shortcuts set last_viewed = now() where administrators_id = :administrators_id and module = :module');
        $Qreset->bind_int(':administrators_id', $data['admin_id']);
        $Qreset->bind_value(':module', $data['application']);
        $Qreset->execute();
        return $Qreset->row_count() === 1 || !$Qreset->is_error();
    }
}