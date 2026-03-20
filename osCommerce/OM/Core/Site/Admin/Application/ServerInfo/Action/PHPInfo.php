<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Server_Info\Action;

use Os_Commerce\OM\Core\Application_Abstract;
class Php_Info
{
    public static function execute(Application_Abstract $application)
    {
        phpinfo();
        exit;
    }
}