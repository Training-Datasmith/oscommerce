<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Languages\Action\Save;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Languages\Languages;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $data = ['id' => $_GET['id'], 'name' => $_POST['name'], 'code' => $_POST['code'], 'locale' => $_POST['locale'], 'charset' => $_POST['charset'], 'date_format_short' => $_POST['date_format_short'], 'date_format_long' => $_POST['date_format_long'], 'time_format' => $_POST['time_format'], 'text_direction' => $_POST['text_direction'], 'currencies_id' => $_POST['currencies_id'], 'numeric_separator_decimal' => $_POST['numeric_separator_decimal'], 'numeric_separator_thousands' => $_POST['numeric_separator_thousands'], 'parent_id' => $_POST['parent_id'], 'sort_order' => $_POST['sort_order'], 'set_default' => isset($_POST['default']) && $_POST['default'] == 'on'];
        if (Languages::update($data)) {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_success_action_performed'), 'success');
        } else {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_action_not_performed'), 'error');
        }
        OSCOM::redirect(OSCOM::get_link());
    }
}