<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Tax_Classes\Action;

use Os_Commerce\OM\Core\Application_Abstract;
class Entry_Save
{
    public static function execute(Application_Abstract $application)
    {
        if (isset($_GET['rID']) && is_numeric($_GET['rID'])) {
            $application->set_page_content('entries_edit.php');
        } else {
            $application->set_page_content('entries_new.php');
        }
    }
}