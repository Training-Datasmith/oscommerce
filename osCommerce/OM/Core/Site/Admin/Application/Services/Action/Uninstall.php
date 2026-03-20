<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Services\Action;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Services\Services;
/**
 * @since v3.0.2
 */
class Uninstall
{
    public static function execute(Application_Abstract $application)
    {
        $data = HTML::sanitize(basename($_GET['code']));
        if (!Services::exists($data) || Services::get($data, 'uninstallable') !== true) {
            OSCOM::redirect(OSCOM::get_link());
        }
    }
}