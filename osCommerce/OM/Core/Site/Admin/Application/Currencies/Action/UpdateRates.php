<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Currencies\Action;

use Os_Commerce\OM\Core\Application_Abstract;
class Update_Rates
{
    public static function execute(Application_Abstract $application)
    {
        $application->set_page_content('update_rates.php');
    }
}