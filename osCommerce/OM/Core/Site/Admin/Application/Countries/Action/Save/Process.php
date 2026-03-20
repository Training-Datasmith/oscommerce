<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Countries\Action\Save;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Countries\Countries;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $data = ['name' => $_POST['countries_name'], 'iso_code_2' => $_POST['countries_iso_code_2'], 'iso_code_3' => $_POST['countries_iso_code_3'], 'address_format' => $_POST['address_format']];
        if (Countries::save(isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null, $data)) {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_success_action_performed'), 'success');
        } else {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_action_not_performed'), 'error');
        }
        OSCOM::redirect(OSCOM::get_link());
    }
}