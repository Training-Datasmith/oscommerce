<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Server_Info\Model;

use Os_Commerce\OM\Core\DateTime;
use Os_Commerce\OM\Core\OSCOM;
class Get_All
{
    public static function execute()
    {
        $result = [];
        $db_time = OSCOM::call_db('Admin\ServerInfo\GetTime');
        $db_uptime = OSCOM::call_db('Admin\ServerInfo\GetUptime');
        $db_version = OSCOM::call_db('Admin\ServerInfo\GetVersion');
        $uptime = '---';
        if (!in_array('exec', explode(',', str_replace(' ', '', ini_get('disable_functions'))))) {
            $uptime = @exec('uptime');
        }
        $data = [['key' => 'date', 'title' => OSCOM::get_def('field_server_date'), 'value' => DateTime::get_short(null, true)], ['key' => 'system', 'title' => OSCOM::get_def('field_server_operating_system'), 'value' => php_uname('s') . ' ' . php_uname('r')], ['key' => 'host', 'title' => OSCOM::get_def('field_server_host'), 'value' => php_uname('n') . ' (' . gethostbyname(php_uname('n')) . ')'], ['key' => 'uptime', 'title' => OSCOM::get_def('field_server_up_time'), 'value' => $uptime], ['key' => 'http_server', 'title' => OSCOM::get_def('field_http_server'), 'value' => $_SERVER['SERVER_SOFTWARE']], ['key' => 'php', 'title' => OSCOM::get_def('field_php_version'), 'value' => 'PHP v' . PHP_VERSION . ' / Zend v' . zend_version()], ['key' => 'db_server', 'title' => OSCOM::get_def('field_database_host'), 'value' => OSCOM::get_config('db_server') . ' (' . gethostbyname(OSCOM::get_config('db_server')) . ')'], ['key' => 'db_version', 'title' => OSCOM::get_def('field_database_version'), 'value' => $db_version], ['key' => 'db_date', 'title' => OSCOM::get_def('field_database_date'), 'value' => DateTime::get_short($db_time, true)], ['key' => 'db_uptime', 'title' => OSCOM::get_def('field_database_up_time'), 'value' => $db_uptime]];
        $result['entries'] = $data;
        $result['total'] = count($data);
        return $result;
    }
}