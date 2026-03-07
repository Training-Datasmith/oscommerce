<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\Model;

use osCommerce\OM\Core\Cache;
use osCommerce\OM\Core\OSCOM;

class saveEntry
{
    public static function execute($data)
    {
        if (OSCOM::callDB('Admin\Configuration\EntrySave', $data)) {
            Cache::clear('configuration');

            return true;
        }

        return false;
    }
}
