<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin;

use Os_Commerce\OM\Core\Registry;
abstract class Application_Abstract extends \Os_Commerce\OM\Core\Application_Abstract
{
    protected $_link_to = true;
    protected $_group;
    protected $_icon = 'default.png';
    protected $_title;
    protected $_sort_order;
    abstract protected function initialize();
    abstract protected function process();
    public function __construct($process = true)
    {
        $this->ignore_action(Registry::get('Session')->get_name());
        $this->initialize();
        if ($process === true) {
            $this->process();
            $this->run_actions();
        }
    }
    public function can_link_to()
    {
        return $this->_link_to;
    }
    public function get_group()
    {
        return $this->_group;
    }
    public function get_icon()
    {
        return $this->_icon;
    }
    public function get_title()
    {
        return $this->_title;
    }
    public function get_sort_order()
    {
        return $this->_sort_order;
    }
}