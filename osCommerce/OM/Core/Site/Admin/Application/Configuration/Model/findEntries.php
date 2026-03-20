<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Configuration\Model;

use Os_Commerce\OM\Core\OSCOM;
class Find_Entries
{
    public static function execute($search, $group_id)
    {
        $data = ['group_id' => $group_id, 'search' => $search];
        return OSCOM::call_db('Admin\Configuration\EntryFind', $data);
    }
}