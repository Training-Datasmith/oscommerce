<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Core_Update\Model;

use Glob_Iterator;
use Os_Commerce\OM\Core\OSCOM;
/**
 * @since v3.0.2
 */
class Get_Logs
{
    public static function execute()
    {
        $result = [];
        $it = new Glob_Iterator(OSCOM::BASE_DIRECTORY . 'Work/Logs/update-*.txt');
        foreach ($it as $f) {
            $result[] = $f->get_filename();
        }
        return $result;
    }
}