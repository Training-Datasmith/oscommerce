<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Payment_Modules\Model;

use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
class Get_Installed
{
    public static function execute()
    {
        $OSCOM_Language = Registry::get('Language');
        $result = OSCOM::call_db('Admin\PaymentModules\GetAll');
        foreach ($result['entries'] as &$module) {
            $class = 'osCommerce\OM\Core\Site\Admin\Module\Payment\\' . $module['code'];
            $OSCOM_Language->inject_definitions('modules/payment/' . $module['code'] . '.xml');
            $OSCOM_PM = new $class();
            $module['code'] = $OSCOM_PM->get_code();
            $module['title'] = $OSCOM_PM->get_title();
            $module['sort_order'] = $OSCOM_PM->get_sort_order();
            $module['status'] = $OSCOM_PM->is_installed() && $OSCOM_PM->is_enabled();
        }
        return $result;
    }
}