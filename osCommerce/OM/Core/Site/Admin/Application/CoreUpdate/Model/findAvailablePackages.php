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
class Find_Available_Packages
{
    public static function execute($search)
    {
        $result = Core_Update::get_available_packages();
        foreach ($result['entries'] as $k => $v) {
            if (strpos($v['version'], $search) === false) {
                unset($result['entries'][$k]);
            }
        }
        $result['total'] = count($result['entries']);
        return $result;
    }
}