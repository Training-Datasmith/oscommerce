<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Payment_Modules\Action\Install;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Payment_Modules\Payment_Modules;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $data = HTML::sanitize(basename($_GET['code']));
        if (Payment_Modules::install($data)) {
            OSCOM::redirect(OSCOM::get_link(null, null, 'Save&code=' . $_GET['code']));
        } else {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_action_not_performed'), 'error');
            OSCOM::redirect(OSCOM::get_link());
        }
    }
}