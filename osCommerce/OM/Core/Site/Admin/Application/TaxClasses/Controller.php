<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Tax_Classes;

use Os_Commerce\OM\Core\OSCOM;
class Controller extends \Os_Commerce\OM\Core\Site\Admin\Application_Abstract
{
    protected $_group = 'configuration';
    protected $_icon = 'taxclasses.png';
    protected $_sort_order = 800;
    protected function initialize()
    {
        $this->_title = OSCOM::get_def('app_title');
    }
    protected function process()
    {
        $this->_page_title = OSCOM::get_def('heading_title');
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $this->_page_contents = 'entries.php';
            $this->_page_title .= ': ' . Tax_Classes::get($_GET['id'], 'tax_class_title');
        }
    }
}