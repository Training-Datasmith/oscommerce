<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Services\Model;

/**
 * @since v3.0.2
 */
class Get_Installed
{
    public static function execute()
    {
        $result = [];
        $result['entries'] = [];
        foreach (explode(';', MODULE_SERVICES_INSTALLED) as $sm) {
            $result['entries'][] = ['code' => $sm];
        }
        $result['total'] = count($result['entries']);
        foreach ($result['entries'] as &$module) {
            $class = 'osCommerce\OM\Core\Site\Admin\Module\Service\\' . $module['code'];
            $OSCOM_SM = new $class();
            $module['code'] = $OSCOM_SM->get_code();
            $module['title'] = $OSCOM_SM->get_title();
            $module['description'] = $OSCOM_SM->get_description();
            $module['uninstallable'] = $OSCOM_SM->is_uninstallable();
            $module['has_keys'] = $OSCOM_SM->has_keys();
        }
        return $result;
    }
}