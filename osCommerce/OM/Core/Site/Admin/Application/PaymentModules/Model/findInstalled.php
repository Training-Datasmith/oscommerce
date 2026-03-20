<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Payment_Modules\Model;

use Os_Commerce\OM\Core\Site\Admin\Application\Payment_Modules\Payment_Modules;
class Find_Installed
{
    public static function execute($search)
    {
        $modules = Payment_Modules::get_installed();
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