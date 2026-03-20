<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Payment_Modules\Model;

use Os_Commerce\OM\Core\Registry;
class get
{
    public static function execute($code)
    {
        $OSCOM_Language = Registry::get('Language');
        $class = 'osCommerce\OM\Core\Site\Admin\Module\Payment\\' . $code;
        $OSCOM_Language->inject_definitions('modules/payment/' . $code . '.xml');
        $OSCOM_PM = new $class();
        $result = ['code' => $OSCOM_PM->get_code(), 'title' => $OSCOM_PM->get_title(), 'sort_order' => $OSCOM_PM->get_sort_order(), 'status' => $OSCOM_PM->is_enabled(), 'keys' => $OSCOM_PM->get_keys()];
        return $result;
    }
}