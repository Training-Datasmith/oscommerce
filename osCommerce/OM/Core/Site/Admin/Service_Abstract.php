<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin;

use Os_Commerce\OM\Core\Registry;
/**
 * @since v3.0.2
 */
abstract class Service_Abstract
{
    protected $code;
    protected $title;
    protected $description;
    protected $uninstallable = true;
    protected $depends;
    protected $precedes;
    abstract protected function initialize();
    public function __construct()
    {
        $OSCOM_Language = Registry::get('Language');
        $module_class = explode('\\', get_called_class());
        $this->code = end($module_class);
        $OSCOM_Language->load_ini_file('modules/Service/' . $this->code . '.php');
        $this->initialize();
    }
    public function get_code()
    {
        return $this->code;
    }
    public function get_title()
    {
        return $this->title;
    }
    public function get_description()
    {
        return $this->description;
    }
    public function is_uninstallable()
    {
        return $this->uninstallable;
    }
    public function has_keys()
    {
        $keys = $this->keys();
        return is_array($keys) && !empty($keys);
    }
    public function install()
    {
        return false;
    }
    public function remove()
    {
        return false;
    }
    public function keys()
    {
        return false;
    }
}