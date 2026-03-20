<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin;

use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
class Template extends \Os_Commerce\OM\Core\Template
{
    public function __construct()
    {
        $this->set('oscom');
    }
    public function get_icon($size = 16, $icon = null, $title = null)
    {
        if (!isset($icon)) {
            $icon = $this->_application->get_icon();
        }
        return HTML::image(OSCOM::get_public_site_link('images/applications/' . $size . '/' . $icon), $title, $size, $size);
    }
}