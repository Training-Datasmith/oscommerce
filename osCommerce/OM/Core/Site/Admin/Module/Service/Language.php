<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Module\Service;

use Os_Commerce\OM\Core\OSCOM;
/**
 * @since v3.0.2
 */
class Language extends \Os_Commerce\OM\Core\Site\Admin\Service_Abstract
{
    public $uninstallable = false;
    public $depends = 'Session';
    protected function initialize()
    {
        $this->title = OSCOM::get_def('services_language_title');
        $this->description = OSCOM::get_def('services_language_description');
    }
}