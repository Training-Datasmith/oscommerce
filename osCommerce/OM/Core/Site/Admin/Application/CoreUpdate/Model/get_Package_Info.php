<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Core_Update\Model;

use Os_Commerce\OM\Core\OSCOM;
use Phar;
class Get_Package_Info
{
    public static function execute($key = null)
    {
        $phar_can_open = true;
        try {
            $phar = new Phar(OSCOM::BASE_DIRECTORY . 'Work/CoreUpdate/update.phar');
        } catch (\Exception $e) {
            $phar_can_open = false;
            trigger_error($e->get_message());
        }
        if ($phar_can_open === true) {
            $result = $phar->get_metadata();
            if (isset($key)) {
                $result = $result[$key] ?: null;
            }
            return $result;
        }
        return false;
    }
}