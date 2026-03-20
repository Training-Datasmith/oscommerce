<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core;

abstract class Application_Abstract
{
    protected $_page_contents = 'main.php';
    protected $_page_title;
    /**
     * @since v3.0.3
     */
    protected $_ignored_actions = [];
    /**
     * @since v3.0.3
     */
    protected $_current_action;
    public function __construct()
    {
        $this->initialize();
        $this->run_actions();
    }
    public function get_page_title()
    {
        return $this->_page_title;
    }
    public function set_page_title($title)
    {
        $this->_page_title = $title;
    }
    public function get_page_content()
    {
        return $this->_page_contents;
    }
    public function set_page_content($filename)
    {
        $this->_page_contents = $filename;
    }
    public function site_application_action_exists($action)
    {
        return class_exists('osCommerce\OM\Core\Site\\' . OSCOM::get_site() . '\Application\\' . OSCOM::get_site_application() . '\Action\\' . $action);
    }
    /**
     * @since v3.0.3
     */
    public function ignore_action($key)
    {
        $this->_ignored_actions[] = $key;
    }
    /**
     * @since v3.0.3
     */
    public function run_actions()
    {
        $action = null;
        $action_index = 1;
        if (count($_GET) > 1) {
            $requested_action = HTML::sanitize(basename(key(array_slice($_GET, 1, 1, true))));
            if ($requested_action == OSCOM::get_site_application()) {
                $requested_action = null;
                if (count($_GET) > 2) {
                    $requested_action = HTML::sanitize(basename(key(array_slice($_GET, 2, 1, true))));
                    $action_index = 2;
                }
            }
            if (!empty($requested_action) && self::site_application_action_exists($requested_action)) {
                $this->_current_action = $action = $requested_action;
            }
        }
        if (isset($action)) {
            call_user_func(['osCommerce\OM\Core\Site\\' . OSCOM::get_site() . '\Application\\' . OSCOM::get_site_application() . '\Action\\' . $action, 'execute'], $this);
            $action_index++;
            if ($action_index < count($_GET)) {
                $action = [$action];
                for ($i = $action_index, $n = count($_GET); $i < $n; $i++) {
                    $subaction = HTML::sanitize(basename(key(array_slice($_GET, $i, 1, true))));
                    if (!in_array($subaction, $this->_ignored_actions) && self::site_application_action_exists(implode('\\', $action) . '\\' . $subaction)) {
                        call_user_func(['osCommerce\OM\Core\Site\\' . OSCOM::get_site() . '\Application\\' . OSCOM::get_site_application() . '\Action\\' . implode('\\', $action) . '\\' . $subaction, 'execute'], $this);
                        $action[] = $subaction;
                        $this->_current_action = $subaction;
                    } else {
                        break;
                    }
                }
            }
        }
    }
    /**
     * @since v3.0.3
     */
    public function get_current_action()
    {
        return $this->_current_action;
    }
}