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
class save
{
    public static function execute($id = null, $data)
    {
        if (is_numeric($id)) {
            $data['id'] = $id;
        }
        if (OSCOM::call_db('Admin\CreditCards\Save', $data)) {
            Cache::clear('credit-cards');
            return true;
        }
        return false;
    }
}