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
class Delete_Definition
{
    public static function execute($id)
    {
        $def = Languages::get_definition($id);
        $data = ['id' => $id];
        if (OSCOM::call_db('Admin\Languages\DeleteDefinition', $data)) {
            Cache::clear('languages-' . Languages::get($def['languages_id'], 'code') . '-' . $def['content_group']);
            return true;
        }
        return false;
    }
}