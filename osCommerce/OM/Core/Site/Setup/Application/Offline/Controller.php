<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Setup\Application\Offline;

use Os_Commerce\OM\Core\OSCOM;
class Controller extends \Os_Commerce\OM\Core\Site\Setup\Application_Abstract
{
    protected function initialize()
    {
        $this->_page_title = OSCOM::get_def('page_title_authorization_required');
    }
}