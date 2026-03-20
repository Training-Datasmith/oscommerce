<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Error_Log\Model;

use Os_Commerce\OM\Core\Error_Handler;
class delete
{
    public static function execute()
    {
        Error_Handler::clear();
        return true;
    }
}