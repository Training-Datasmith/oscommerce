<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin;

abstract class Index_Modules_Abstract
{
    protected $_title;
    protected $_title_link;
    protected $_data;
    public function get_title()
    {
        return $this->_title;
    }
    public function get_title_link()
    {
        return $this->_title_link;
    }
    public function has_title_link()
    {
        return isset($this->_title_link) && !empty($this->_title_link);
    }
    public function get_data()
    {
        return $this->_data;
    }
    public function has_data()
    {
        return isset($this->_data) && !empty($this->_data);
    }
}