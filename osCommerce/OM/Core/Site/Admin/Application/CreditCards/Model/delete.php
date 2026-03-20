<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Credit_Cards\Model;

use Os_Commerce\OM\Core\Cache;
use Os_Commerce\OM\Core\OSCOM;
class delete
{
    public static function execute($id)
    {
        $data = ['id' => $id];
        if (OSCOM::call_db('Admin\CreditCards\Delete', $data)) {
            Cache::clear('credit-cards');
            return true;
        }
        return false;
    }
}