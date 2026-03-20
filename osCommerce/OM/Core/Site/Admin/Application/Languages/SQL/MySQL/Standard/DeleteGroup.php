<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Languages\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Delete_Group
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qdel = $OSCOM_PDO->prepare('delete from :table_languages_definitions where content_group = :content_group');
        $Qdel->bind_value(':content_group', $data['group']);
        $Qdel->execute();
        return $Qdel->row_count() > 0;
    }
}