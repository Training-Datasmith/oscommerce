<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Model;

use osCommerce\OM\Core\Cache;
use osCommerce\OM\Core\OSCOM;

class deleteGroup
{
    public static function execute($group)
    {
        $data = ['group' => $group];

        if (OSCOM::callDB('Admin\Languages\DeleteGroup', $data)) {
            Cache::clear('languages');

            return true;
        }

        return false;
    }
}
