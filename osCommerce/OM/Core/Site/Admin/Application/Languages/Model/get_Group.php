<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Languages\Model;

use Os_Commerce\OM\Core\OSCOM;
class Get_Group
{
    public static function execute($group)
    {
        $data = ['group' => $group];
        return OSCOM::call_db('Admin\Languages\GetGroup', $data);
    }
}