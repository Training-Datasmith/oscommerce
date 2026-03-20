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
function osc_cfg_set_zones_pulldown_menu($default, $key = null)
{
    $name = !empty($key) ? 'configuration[' . $key . ']' : 'configuration_value';
    $zones_array = [];
    foreach (Address::get_zones() as $zone) {
        $zones_array[] = ['id' => $zone['id'], 'text' => $zone['name'], 'group' => $zone['country_name']];
    }
    return HTML::select_menu($name, $zones_array, $default);
}