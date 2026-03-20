<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Credit_Cards\Action\Save;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Credit_Cards\Credit_Cards;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $data = ['name' => $_POST['credit_card_name'], 'pattern' => $_POST['pattern'], 'status' => isset($_POST['credit_card_status']) && $_POST['credit_card_status'] == '1' ? 1 : 0, 'sort_order' => $_POST['sort_order']];
        if (Credit_Cards::save(isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null, $data)) {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_success_action_performed'), 'success');
        } else {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_action_not_performed'), 'error');
        }
        OSCOM::redirect(OSCOM::get_link());
    }
}