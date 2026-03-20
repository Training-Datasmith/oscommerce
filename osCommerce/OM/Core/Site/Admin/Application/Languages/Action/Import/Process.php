<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Languages\Action\Import;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Languages\Languages;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $data = ['code' => $_POST['language_import'], 'type' => $_POST['import_type']];
        if (Languages::import($data)) {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_success_action_performed'), 'success');
        } else {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_action_not_performed'), 'error');
        }
        OSCOM::redirect(OSCOM::get_link());
    }
}