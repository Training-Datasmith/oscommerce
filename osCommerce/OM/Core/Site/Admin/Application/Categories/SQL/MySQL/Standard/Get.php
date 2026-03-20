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
class Get
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qcategory = $OSCOM_PDO->prepare('select c.*, cd.* from :table_categories c, :table_categories_description cd where c.categories_id = :categories_id and c.categories_id = cd.categories_id and cd.language_id = :language_id');
        $Qcategory->bind_int(':categories_id', $data['id']);
        $Qcategory->bind_int(':language_id', $data['language_id']);
        $Qcategory->execute();
        return $Qcategory->fetch();
    }
}