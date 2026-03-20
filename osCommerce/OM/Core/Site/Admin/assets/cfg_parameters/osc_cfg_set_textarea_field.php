<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
function osc_cfg_set_textarea_field($default, $key = null)
{
    $name = !empty($key) ? 'configuration[' . $key . ']' : 'configuration_value';
    return HTML::textarea_field($name, $default, 35, 5);
}