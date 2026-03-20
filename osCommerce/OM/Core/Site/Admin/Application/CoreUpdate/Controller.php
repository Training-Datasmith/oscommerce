<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Core_Update;

use Os_Commerce\OM\Core\OSCOM;
class Controller extends \Os_Commerce\OM\Core\Site\Admin\Application_Abstract
{
    protected $_group = 'tools';
    protected $_icon = 'coreupdate.png';
    protected $_sort_order = 5;
    protected function initialize()
    {
        $this->_title = OSCOM::get_def('app_title');
    }
    protected function process()
    {
        $this->_page_title = OSCOM::get_def('heading_title');
    }
    /**
     * @since v3.0.2
     */
    public function get_log_list()
    {
        $array = [['id' => '', 'text' => OSCOM::get_def('select_log_to_view'), 'params' => 'disabled="disabled"']];
        foreach (Core_Update::get_logs() as $f) {
            $array[] = ['id' => substr($f, 0, -4), 'text' => substr($f, 0, -4)];
        }
        return $array;
    }
}