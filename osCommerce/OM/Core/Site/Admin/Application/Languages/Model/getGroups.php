<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Model;

use osCommerce\OM\Core\OSCOM;

class getGroups
{
    public static function execute($language_id)
    {
        $data = ['id' => $language_id];

        return OSCOM::callDB('Admin\Languages\GetGroups', $data);
    }
}
