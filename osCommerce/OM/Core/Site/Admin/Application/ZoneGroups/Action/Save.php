<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Zone_Groups\Action;

use Os_Commerce\OM\Core\Application_Abstract;
class Save
{
    public static function execute(Application_Abstract $application)
    {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $application->set_page_content('edit.php');
        } else {
            $application->set_page_content('new.php');
        }
    }
}