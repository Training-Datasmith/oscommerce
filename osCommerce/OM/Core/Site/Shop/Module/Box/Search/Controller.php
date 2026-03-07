<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Shop\Module\Box\Search;

use osCommerce\OM\Core\HTML;
use osCommerce\OM\Core\OSCOM;

class Controller extends \osCommerce\OM\Core\Modules
{
    public $_title;
    public $_code = 'Search';
    public $_author_name = 'osCommerce';
    public $_author_www = 'http://www.oscommerce.com';
    public $_group = 'Box';

    public function __construct()
    {
        $this->_title = OSCOM::getDef('box_search_heading');
    }

    public function initialize()
    {
        $this->_title_link = OSCOM::getLink(null, 'Search');

        $this->_content = '<form name="search" action="' . OSCOM::getLink() . '" method="get">' . HTML::hiddenField('Search', null) .
                          HTML::inputField('Q', null, 'style="width: 80%;" maxlength="30"') . '&nbsp;' . HTML::hiddenSessionIDField() . HTML::button(['icon' => 'search', 'title' => OSCOM::getDef('box_search_heading')]) .
                          '</form>';
    }
}
