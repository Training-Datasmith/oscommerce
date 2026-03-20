<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Error_Log\Model;

use Os_Commerce\OM\Core\DateTime;
use Os_Commerce\OM\Core\Error_Handler;
class Get_All
{
    public static function execute($pageset = 1)
    {
        if (!is_numeric($pageset) || floor($pageset) != $pageset) {
            $pageset = 1;
        }
        $result = ['entries' => [], 'total' => Error_Handler::get_total_entries()];
        foreach (Error_Handler::get_all(MAX_DISPLAY_SEARCH_RESULTS, $pageset) as $row) {
            $result['entries'][] = ['date' => DateTime::get_short(DateTime::from_unix_timestamp($row['timestamp']), true), 'message' => $row['message']];
        }
        return $result;
    }
}