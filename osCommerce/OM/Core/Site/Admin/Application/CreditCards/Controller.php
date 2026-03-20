<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Credit_Cards;

use Os_Commerce\OM\Core\OSCOM;
class Controller extends \Os_Commerce\OM\Core\Site\Admin\Application_Abstract
{
    protected $_group = 'configuration';
    protected $_icon = 'creditcards.png';
    protected $_sort_order = 300;
    protected function initialize()
    {
        $this->_title = OSCOM::get_def('app_title');
    }
    protected function process()
    {
        $this->_page_title = OSCOM::get_def('heading_title');
    }
}