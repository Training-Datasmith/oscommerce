<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Setup;

use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
class Controller implements \Os_Commerce\OM\Core\Site_Interface
{
    protected static $_default_application = 'Index';
    public static function initialize()
    {
        Registry::set('Language', new Language());
        Registry::set('osC_Language', Registry::get('Language'));
        // HPDL to remove
        if (!self::has_access(OSCOM::get_site_application())) {
            OSCOM::redirect(OSCOM::get_link(null, 'Offline'));
        }
        $application = 'osCommerce\OM\Core\Site\Setup\Application\\' . OSCOM::get_site_application() . '\Controller';
        Registry::set('Application', new $application());
        Registry::set('Template', new Template());
        Registry::get('Template')->set_application(Registry::get('Application'));
    }
    public static function get_default_application()
    {
        return self::$_default_application;
    }
    public static function has_access($application)
    {
        if (OSCOM::config_exists('offline') && OSCOM::get_config('offline') == 'true' && $application != 'Offline') {
            return false;
        }
        return true;
    }
}