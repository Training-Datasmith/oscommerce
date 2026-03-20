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
class Get_Groups
{
    public static function execute($language_id)
    {
        $data = ['id' => $language_id];
        return OSCOM::call_db('Admin\Languages\GetGroups', $data);
    }
}