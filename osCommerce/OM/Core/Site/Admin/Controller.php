<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin;

use Os_Commerce\OM\Core\Access;
use Os_Commerce\OM\Core\Cache;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\PDO;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Session;
class Controller implements \Os_Commerce\OM\Core\Site_Interface
{
    protected static $_default_application = 'Dashboard';
    protected static $_guest_applications = ['Dashboard', 'Login'];
    public static function initialize()
    {
        Registry::set('MessageStack', new Message_Stack());
        Registry::set('Cache', new Cache());
        Registry::set('PDO', PDO::initialize());
        foreach (OSCOM::call_db('Shop\GetConfiguration', null, 'Site') as $param) {
            define($param['cfgKey'], $param['cfgValue']);
        }
        Registry::set('Session', Session::load('adminSid'));
        Registry::get('Session')->start();
        Registry::get('MessageStack')->load_from_session();
        Registry::set('Language', new Language());
        if (!self::has_access(OSCOM::get_site_application())) {
            Registry::get('MessageStack')->add('header', 'No access.', 'error');
            OSCOM::redirect(OSCOM::get_link(null, OSCOM::get_default_site_application()));
        }
        $application = 'osCommerce\OM\Core\Site\Admin\Application\\' . OSCOM::get_site_application() . '\Controller';
        Registry::set('Application', new $application());
        Registry::set('Template', new Template());
        Registry::get('Template')->set_application(Registry::get('Application'));
        // HPDL move following checks elsewhere
        // check if a default currency is set
        if (!defined('DEFAULT_CURRENCY')) {
            Registry::get('MessageStack')->add('header', OSCOM::get_def('ms_error_no_default_currency'), 'error');
        }
        // check if a default language is set
        if (!defined('DEFAULT_LANGUAGE')) {
            Registry::get('MessageStack')->add('header', ERROR_NO_DEFAULT_LANGUAGE_DEFINED, 'error');
        }
        if (function_exists('ini_get') && (bool) ini_get('file_uploads') == false) {
            Registry::get('MessageStack')->add('header', OSCOM::get_def('ms_warning_uploads_disabled'), 'warning');
        }
        // check if Work directories are writable
        $work_dirs = [];
        foreach (['Cache', 'CoreUpdate', 'Database', 'Logs', 'Session', 'Temp'] as $w) {
            if (!is_writable(OSCOM::BASE_DIRECTORY . 'Work/' . $w)) {
                $work_dirs[] = $w;
            }
        }
        if (!empty($work_dirs)) {
            Registry::get('MessageStack')->add('header', sprintf(OSCOM::get_def('ms_error_work_directories_not_writable'), OSCOM::BASE_DIRECTORY . 'Work/', implode(', ', $work_dirs)), 'error');
        }
        if (!OSCOM::config_exists('time_zone', 'OSCOM')) {
            Registry::get('MessageStack')->add('header', OSCOM::get_def('ms_warning_time_zone_not_defined'), 'warning');
        }
        if (!OSCOM::config_exists('dir_fs_public', 'OSCOM') || !file_exists(OSCOM::get_config('dir_fs_public', 'OSCOM'))) {
            Registry::get('MessageStack')->add('header', OSCOM::get_def('ms_warning_dir_fs_public_not_defined'), 'warning');
        }
        // check if the upload directory exists
        if (is_dir(OSCOM::get_config('dir_fs_public', 'OSCOM') . 'upload')) {
            if (!is_writeable(OSCOM::get_config('dir_fs_public', 'OSCOM') . 'upload')) {
                Registry::get('MessageStack')->add('header', sprintf(OSCOM::get_def('ms_error_upload_directory_not_writable'), OSCOM::get_config('dir_fs_public', 'OSCOM') . 'upload'), 'error');
            }
        } else {
            Registry::get('MessageStack')->add('header', sprintf(OSCOM::get_def('ms_error_upload_directory_non_existant'), OSCOM::get_config('dir_fs_public', 'OSCOM') . 'upload'), 'error');
        }
    }
    public static function get_default_application()
    {
        return self::$_default_application;
    }
    public static function has_access($application)
    {
        if (!isset($_SESSION[OSCOM::get_site()]['id'])) {
            $redirect = false;
            if ($application != 'Login') {
                $_SESSION[OSCOM::get_site()]['redirect_origin'] = $application;
                $redirect = true;
            }
            if ($redirect === true) {
                OSCOM::redirect(OSCOM::get_link(null, 'Login'));
            }
        }
        return Access::has_access(OSCOM::get_site(), $application);
    }
    public static function get_guest_applications()
    {
        return self::$_guest_applications;
    }
}