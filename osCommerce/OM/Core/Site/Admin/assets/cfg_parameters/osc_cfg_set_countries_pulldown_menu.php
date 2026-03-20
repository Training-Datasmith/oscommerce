<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\Site\Shop\Address;
function osc_cfg_set_countries_pulldown_menu($default, $key = null)
{
    $name = !empty($key) ? 'configuration[' . $key . ']' : 'configuration_value';
    $countries_array = [];
    foreach (Address::get_countries() as $country) {
        $countries_array[] = ['id' => $country['id'], 'text' => $country['name']];
    }
    return HTML::select_menu($name, $countries_array, $default);
}