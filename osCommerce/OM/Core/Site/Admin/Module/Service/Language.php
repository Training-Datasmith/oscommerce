<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Admin\Module\Service;

use osCommerce\OM\Core\OSCOM;

/**
 * @since v3.0.2
 */

class Language extends \osCommerce\OM\Core\Site\Admin\ServiceAbstract
{
    public $uninstallable = false;
    public $depends = 'Session';

    protected function initialize()
    {
        $this->title = OSCOM::getDef('services_language_title');
        $this->description = OSCOM::getDef('services_language_description');
    }
}
