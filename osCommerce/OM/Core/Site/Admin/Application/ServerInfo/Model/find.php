<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Server_Info\Model;

use Os_Commerce\OM\Core\Site\Admin\Application\Server_Info\Server_Info;
class find
{
    public static function execute($search)
    {
        $modules = Server_Info::get_all();
        $result = ['entries' => []];
        foreach ($modules['entries'] as $module) {
            if (stripos($module['key'], $search) !== false || stripos($module['title'], $search) !== false || stripos($module['value'], $search) !== false) {
                $result['entries'][] = $module;
            }
        }
        $result['total'] = count($result['entries']);
        return $result;
    }
}