<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Tax_Classes\Action\Entry_Save;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Tax_Classes\Tax_Classes;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $data = ['zone_id' => $_POST['tax_zone_id'], 'rate' => $_POST['tax_rate'], 'description' => $_POST['tax_description'], 'priority' => $_POST['tax_priority'], 'rate' => $_POST['tax_rate'], 'tax_class_id' => $_GET['id']];
        if (Tax_Classes::save_entry(isset($_GET['rID']) && is_numeric($_GET['rID']) ? $_GET['rID'] : null, $data)) {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_success_action_performed'), 'success');
        } else {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_action_not_performed'), 'error');
        }
        OSCOM::redirect(OSCOM::get_link(null, null, 'id=' . $_GET['id']));
    }
}