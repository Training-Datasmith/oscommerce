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
class Get_Shortcut_Notification
{
    public static function execute($datetime)
    {
        $errors = Error_Handler::get_all(100);
        $from_timestamp = DateTime::get_timestamp($datetime, 'Y-m-d H:i:s');
        $result = 0;
        foreach ($errors as $error) {
            if ($error['timestamp'] > $from_timestamp) {
                $result++;
            }
        }
        return $result;
    }
}