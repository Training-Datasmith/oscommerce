<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Core_Update\Model;

use Os_Commerce\OM\Core\DateTime;
use Os_Commerce\OM\Core\OSCOM;
/**
 * @since v3.0.2
 */
class Get_Log
{
    public static function execute($log)
    {
        $log = basename($log);
        if (substr($log, 0, -4) != '.txt') {
            $log .= '.txt';
        }
        $result = ['entries' => []];
        foreach (file(OSCOM::BASE_DIRECTORY . 'Work/Logs/' . $log) as $l) {
            if (preg_match('/^\[([0-9]{2})-([A-Za-z]{3})-([0-9]{4}) ([0-9]{2}):([0-5][0-9]):([0-5][0-9])\] (.*)$/', $l)) {
                $result['entries'][] = ['date' => DateTime::get_short(DateTime::from_unix_timestamp(DateTime::get_timestamp(substr($l, 1, 20), 'd-M-Y H:i:s')), true), 'message' => substr($l, 23)];
            }
        }
        $result['total'] = count($result['entries']);
        return $result;
    }
}