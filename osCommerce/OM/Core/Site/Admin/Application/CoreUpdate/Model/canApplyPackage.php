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
class Can_Apply_Package
{
    public static function execute()
    {
        $contents = Core_Update::get_package_contents();
        foreach ($contents['entries'] as $file) {
            if ($file['writable'] === false) {
                return false;
            }
        }
        return true;
    }
}