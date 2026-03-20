<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Currencies\Action\Update_Rates;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Currencies\Currencies;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        if (isset($_POST['service']) && ($_POST['service'] == 'oanda' || $_POST['service'] == 'xe')) {
            $results = Currencies::update_rates($_POST['service']);
            foreach ($results[0] as $result) {
                Registry::get('MessageStack')->add(null, sprintf(OSCOM::get_def('ms_error_invalid_currency'), $result['title'], $result['code']), 'error');
            }
            foreach ($results[1] as $result) {
                Registry::get('MessageStack')->add(null, sprintf(OSCOM::get_def('ms_success_currency_updated'), $result['title'], $result['code']), 'success');
            }
        }
        OSCOM::redirect(OSCOM::get_link());
    }
}