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
class Package_Exists
{
    public static function execute($version)
    {
        $versions = Core_Update::get_available_packages();
        foreach ($versions['entries'] as $v) {
            if ($v['version'] == $version) {
                return true;
            }
        }
        return false;
    }
}