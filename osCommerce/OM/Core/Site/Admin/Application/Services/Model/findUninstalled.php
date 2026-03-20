<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Services\Model;

use Os_Commerce\OM\Core\Site\Admin\Application\Services\Services;
/**
 * @since v3.0.2
 */
class Find_Uninstalled
{
    public static function execute($search)
    {
        $modules = Services::get_uninstalled();
        $result = ['entries' => []];
        foreach ($modules['entries'] as $module) {
            if (stripos($module['code'], $search) !== false || stripos($module['title'], $search) !== false) {
                $result['entries'][] = $module;
            }
        }
        $result['total'] = count($result['entries']);
        return $result;
    }
}