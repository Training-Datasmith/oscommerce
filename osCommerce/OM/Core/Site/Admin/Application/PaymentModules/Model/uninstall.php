<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Payment_Modules\Model;

use Os_Commerce\OM\Core\Cache;
use Os_Commerce\OM\Core\Registry;
class uninstall
{
    public static function execute($module)
    {
        $OSCOM_Language = Registry::get('Language');
        $class = 'osCommerce\OM\Core\Site\Admin\Module\Payment\\' . $module;
        if (class_exists($class)) {
            $OSCOM_Language->inject_definitions('modules/payment/' . $module . '.xml');
            $OSCOM_PM = new $class();
            $OSCOM_PM->remove();
            Cache::clear('modules-payment');
            Cache::clear('configuration');
            return true;
        }
        return false;
    }
}