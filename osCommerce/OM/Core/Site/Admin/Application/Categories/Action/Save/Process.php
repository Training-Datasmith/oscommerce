<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Categories\Action\Save;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Categories\Categories;
/**
 * @since v3.0.2
 */
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $data = ['name' => $_POST['categories_name'], 'image' => isset($_POST['cImageSelected']) ? $_POST['cImageSelected'] : null, 'parent_id' => $_POST['parent_id']];
        if (Categories::save(isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null, $data)) {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_success_action_performed'), 'success');
        } else {
            Registry::get('MessageStack')->add(null, OSCOM::get_def('ms_error_action_not_performed'), 'error');
        }
        OSCOM::redirect(OSCOM::get_link(null, null, 'cid=' . $application->get_current_category_id()));
    }
}