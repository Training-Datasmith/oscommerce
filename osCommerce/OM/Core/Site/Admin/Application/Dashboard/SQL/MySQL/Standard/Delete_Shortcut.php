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
class Delete_Shortcut
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qsc = $OSCOM_PDO->prepare('delete from :table_administrator_shortcuts where administrators_id = :administrators_id and module = :module');
        $Qsc->bind_int(':administrators_id', $data['admin_id']);
        $Qsc->bind_value(':module', $data['application']);
        $Qsc->execute();
        return $Qsc->row_count() === 1 || !$Qsc->is_error();
    }
}