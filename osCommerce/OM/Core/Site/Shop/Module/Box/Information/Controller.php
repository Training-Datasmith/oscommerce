<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Shop\Module\Box\Information;

use osCommerce\OM\Core\HTML;
use osCommerce\OM\Core\OSCOM;

class Controller extends \osCommerce\OM\Core\Modules
{
    public $_title;
    public $_code = 'Information';
    public $_author_name = 'osCommerce';
    public $_author_www = 'http://www.oscommerce.com';
    public $_group = 'Box';

    public function __construct()
    {
        $this->_title = OSCOM::getDef('box_information_heading');
    }

    public function initialize()
    {
        $this->_title_link = OSCOM::getLink(null, 'Info');

        $this->_content = '<ol style="list-style: none; margin: 0; padding: 0;">' .
                          '  <li>' . HTML::link(OSCOM::getLink(null, 'Info', 'Shipping'), OSCOM::getDef('box_information_shipping')) . '</li>' .
                          '  <li>' . HTML::link(OSCOM::getLink(null, 'Info', 'Privacy'), OSCOM::getDef('box_information_privacy')) . '</li>' .
                          '  <li>' . HTML::link(OSCOM::getLink(null, 'Info', 'Conditions'), OSCOM::getDef('box_information_conditions')) . '</li>' .
                          '  <li>' . HTML::link(OSCOM::getLink(null, 'Info', 'Contact'), OSCOM::getDef('box_information_contact')) . '</li>' .
                          '  <li>' . HTML::link(OSCOM::getLink(null, 'Info', 'Sitemap'), OSCOM::getDef('box_information_sitemap')) . '</li>' .
                          '</ol>';
    }
}
