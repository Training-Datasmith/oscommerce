<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Dashboard\Model;

use Os_Commerce\OM\Core\OSCOM;
class Update_App_Date_Opened
{
    public static function execute($admin_id, $application)
    {
        $data = ['admin_id' => $admin_id, 'application' => $application];
        return OSCOM::call_db('Admin\Dashboard\UpdateAppLastOpened', $data);
    }
}