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
use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;

class delete
{
    public static function execute($id)
    {
        $data = ['id' => $id];

        if ((Languages::get($id, 'code') != DEFAULT_LANGUAGE) && OSCOM::callDB('Admin\Languages\Delete', $data)) {
            Cache::clear('languages');

            return true;
        }

        return false;
    }
}
