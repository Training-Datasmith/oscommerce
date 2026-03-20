<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Zone_Groups\Model;

use Os_Commerce\OM\Core\OSCOM;
class get
{
    public static function execute($id, $key = null)
    {
        $data = ['id' => $id];
        $result = OSCOM::call_db('Admin\ZoneGroups\Get', $data);
        if (isset($key)) {
            $result = $result[$key] ?: null;
        }
        return $result;
    }
}