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
class get
{
    public static function execute($code, $key = null)
    {
        $class = 'osCommerce\OM\Core\Site\Admin\Module\Service\\' . $code;
        $OSCOM_SM = new $class();
        $result = ['code' => $OSCOM_SM->get_code(), 'title' => $OSCOM_SM->get_title(), 'description' => $OSCOM_SM->get_description(), 'uninstallable' => $OSCOM_SM->is_uninstallable(), 'keys' => $OSCOM_SM->keys()];
        if (isset($key)) {
            $result = $result[$key] ?: null;
        }
        return $result;
    }
}