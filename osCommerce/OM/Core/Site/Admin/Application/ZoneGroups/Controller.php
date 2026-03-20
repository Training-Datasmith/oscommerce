<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Zone_Groups;

use Os_Commerce\OM\Core\OSCOM;
class Controller extends \Os_Commerce\OM\Core\Site\Admin\Application_Abstract
{
    protected $_group = 'configuration';
    protected $_icon = 'zonegroups.png';
    protected $_sort_order = 700;
    protected function initialize()
    {
        $this->_title = OSCOM::get_def('app_title');
    }
    protected function process()
    {
        $this->_page_title = OSCOM::get_def('heading_title');
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $this->_page_contents = 'entries.php';
            $this->_page_title .= ': ' . Zone_Groups::get($_GET['id'], 'geo_zone_name');
        }
    }
}