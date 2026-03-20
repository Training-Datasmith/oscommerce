<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Languages\Model;

use Os_Commerce\OM\Core\Cache;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Languages\Languages;
class Update_Definition
{
    public static function execute($data)
    {
        $definitions = $data['definitions'];
        unset($data['definitions']);
        foreach ($definitions as $key => $value) {
            $data['key'] = $key;
            $data['value'] = $value;
            OSCOM::call_db('Admin\Languages\UpdateDefinition', $data);
            Cache::clear('languages-' . Languages::get($data['language_id'], 'code') . '-' . $data['group']);
        }
        return true;
    }
}