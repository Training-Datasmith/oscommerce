<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Tax_Classes\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
class Get_Total_Products
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $Qtotal = $OSCOM_PDO->prepare('select count(*) as total from :table_products where products_tax_class_id = :products_tax_class_id');
        $Qtotal->bind_int(':products_tax_class_id', $data['id']);
        $Qtotal->execute();
        $result = $Qtotal->fetch();
        return $result['total'];
    }
}