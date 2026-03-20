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
use Os_Commerce\OM\Core\Http_Request;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
class Get_Available_Packages
{
    public static function execute()
    {
        $OSCOM_Cache = Registry::get('Cache');
        $result = ['entries' => []];
        if ($OSCOM_Cache->read('coreupdate-availablepackages', 360)) {
            $versions = $OSCOM_Cache->get_cache();
        } else {
            $versions = Http_Request::get_response(['url' => 'http://www.oscommerce.com/version/online_merchant/3', 'method' => 'get']);
            $OSCOM_Cache->write($versions);
        }
        $versions_array = explode("\n", $versions);
        $counter = 0;
        foreach ($versions_array as $v) {
            $v_info = explode('|', $v);
            if (version_compare(OSCOM::get_version(), $v_info[0], '<')) {
                $result['entries'][] = ['key' => $counter, 'version' => $v_info[0], 'date' => DateTime::get_short(DateTime::from_unix_timestamp(DateTime::get_timestamp($v_info[1], 'Ymd'))), 'announcement' => $v_info[2], 'update_package' => isset($v_info[3]) ? $v_info[3] : null];
                $counter++;
            }
        }
        usort($result['entries'], function ($a, $b) {
            return version_compare($a['version'], $b['version'], '>');
        });
        $result['total'] = count($result['entries']);
        return $result;
    }
}