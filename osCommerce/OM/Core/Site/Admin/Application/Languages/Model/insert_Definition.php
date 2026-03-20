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
class Insert_Definition
{
    public static function execute($data)
    {
        $languages = Languages::get_all(-1);
        $languages = $languages['entries'];
        $values = $data['values'];
        unset($data['values']);
        foreach ($languages as $l) {
            $data['language_id'] = $l['languages_id'];
            $data['value'] = $values[$l['languages_id']];
            OSCOM::call_db('Admin\Languages\InsertDefinition', $data);
            Cache::clear('languages-' . $l['code'] . '-' . $data['group']);
        }
        return true;
    }
}