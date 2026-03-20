<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Setup;

use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
abstract class Application_Abstract extends \Os_Commerce\OM\Core\Application_Abstract
{
    abstract protected function initialize();
    public function __construct()
    {
        $this->initialize();
        if (isset($_GET['action']) && !empty($_GET['action'])) {
            $action = HTML::sanitize(basename($_GET['action']));
            if (class_exists('osCommerce\OM\Core\Site\\' . OSCOM::get_site() . '\Application\\' . OSCOM::get_site_application() . '\Action\\' . $action)) {
                call_user_func(['osCommerce\OM\Core\Site\\' . OSCOM::get_site() . '\Application\\' . OSCOM::get_site_application() . '\Action\\' . $action, 'execute'], $this);
            }
        }
    }
}