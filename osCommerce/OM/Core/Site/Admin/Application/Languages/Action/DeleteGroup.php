<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Languages\Action;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\Site\Admin\Application\Languages\Languages;
class Delete_Group
{
    public static function execute(Application_Abstract $application)
    {
        if (Languages::is_group($_GET['id'], $_GET['group'])) {
            $application->set_page_content('groups_delete.php');
        }
    }
}