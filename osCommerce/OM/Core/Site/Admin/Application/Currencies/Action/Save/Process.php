<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Currencies\Action\Save;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Currencies\Currencies;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $data = ['id' => isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null, 'title' => $_POST['title'], 'code' => $_POST['code'], 'symbol_left' => $_POST['symbol_left'], 'symbol_right' => $_POST['symbol_right'], 'decimal_places' => $_POST['decimal_places'], 'value' => $_POST['value'], 'set_default' => isset($_POST['default']) && $_POST['default'] == 'on' || isset($_POST['is_default']) && $_POST['is_default'] == 'true' && $_POST['code'] != DEFAULT_CURRENCY];
        if (Currencies::save($data)) {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_success_action_performed'), 'success');
        } else {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_action_not_performed'), 'error');
        }
        OSCOM::redirect(OSCOM::get_link());
    }
}