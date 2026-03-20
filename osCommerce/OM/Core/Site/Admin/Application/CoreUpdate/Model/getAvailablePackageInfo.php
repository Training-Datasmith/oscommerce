<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Core_Update\Model;

use Os_Commerce\OM\Core\Site\Admin\Application\Core_Update\Core_Update;
class Get_Available_Package_Info
{
    public static function execute($key = null)
    {
        $versions = Core_Update::get_available_packages();
        if (!empty($versions['entries'])) {
            if (!empty($key) && isset($versions['entries'][0][$key])) {
                return $versions['entries'][0][$key];
            } else {
                return $versions['entries'][0];
            }
        }
        return false;
    }
}