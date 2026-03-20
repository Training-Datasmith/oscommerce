<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\Site\Shop\Weight;
function osc_cfg_set_weight_classes_pulldown_menu($default, $key = null)
{
    $name = empty($key) ? 'configuration_value' : 'configuration[' . $key . ']';
    $weight_class_array = [];
    foreach (Weight::get_classes() as $class) {
        $weight_class_array[] = ['id' => $class['id'], 'text' => $class['title']];
    }
    return HTML::select_menu($name, $weight_class_array, $default);
}