<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Currencies\Model;

use Os_Commerce\OM\Core\Cache;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Currencies\Currencies;
class Update_Rates
{
    public static function execute($service)
    {
        $updated = ['0' => [], '1' => []];
        $currencies = Currencies::get_all(-1);
        $currencies = $currencies['entries'];
        foreach ($currencies as $currency) {
            $data = ['id' => $currency['currencies_id'], 'rate' => call_user_func('quote_' . $service . '_currency', $currency['code'])];
            if (!empty($data['rate']) && OSCOM::call_db('Admin\Currencies\UpdateRate', $data)) {
                $updated[1][] = ['title' => $currency['title'], 'code' => $currency['code']];
            } else {
                $updated[0][] = ['title' => $currency['title'], 'code' => $currency['code']];
            }
        }
        if (!empty($updated[1])) {
            Cache::clear('currencies');
        }
        return $updated;
    }
}