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
/**
 * @since v3.0.2
 */
class Find_Log
{
    public static function execute($log, $search)
    {
        $data = Core_Update::get_log($log);
        $result = ['entries' => []];
        foreach ($data['entries'] as $l) {
            if (stripos($l['message'], $search) !== false) {
                $result['entries'][] = $l;
            }
        }
        $result['total'] = count($result['entries']);
        return $result;
    }
}