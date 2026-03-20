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
class Batch_Delete_Entries
{
    public static function execute(Application_Abstract $application)
    {
        if (isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch'])) {
            $application->set_page_content('entries_batch_delete.php');
        }
    }
}