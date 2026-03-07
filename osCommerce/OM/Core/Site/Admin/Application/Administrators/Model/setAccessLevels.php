<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Admin\Application\Administrators\Model;

use osCommerce\OM\Core\OSCOM;
use osCommerce\OM\Core\Site\Admin\Application\Administrators\Administrators;

class setAccessLevels
{
    public static function execute($id, $modules, $mode = Administrators::ACCESS_MODE_ADD)
    {
        $data = ['id' => $id,
                      'modules' => $modules,
                      'mode' => $mode];

        if (in_array('0', $data['modules'])) {
            $data['modules'] = ['*'];
        }

        return OSCOM::callDB('Admin\Administrators\SavePermissions', $data);
    }
}
