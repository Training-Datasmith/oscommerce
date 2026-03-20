<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core;

/**
 * The osC_Access manages the permission levels of administrators who have access to the Administration Tool
 */
class Access
{
    /**
     * Holds the group code for the current access module
     *
     * @var string
     * @access protected
     */
    protected $_group = 'misc';
    /**
     * Holds the icon for the current access module
     *
     * @var string
     * @access protected
     */
    protected $_icon = 'default.png';
    /**
     * Holds the title of the current access module
     *
     * @var string
     * @access protected
     */
    protected $_title;
    /**
     * Holds the sort ordering number for the current access module
     *
     * @var int
     * @access protected
     */
    protected $_sort_order = 0;
    /**
     * Return the Administration Tool Application modules the administrator has access to
     *
     * @param int $id The ID of the administrator
     * @access public
     * @return array
     */
    public static function get_user_levels($id, $site = null)
    {
        if (empty($site)) {
            $site = OSCOM::get_site();
        }
        $data = ['id' => $id];
        $applications = [];
        foreach (OSCOM::call_db('GetAccessUserLevels', $data, 'Core') as $am) {
            $applications[] = $am['module'];
        }
        if (in_array('*', $applications)) {
            $applications = [];
            $d_lapps = new Directory_Listing(OSCOM::BASE_DIRECTORY . 'Core/Site/' . $site . '/Application');
            $d_lapps->set_include_files(false);
            foreach ($d_lapps->get_files() as $file) {
                if (preg_match('/[A-Z]/', substr($file['name'], 0, 1)) && !in_array($file['name'], call_user_func(['osCommerce\OM\Core\Site\\' . $site . '\Controller', 'getGuestApplications'])) && file_exists($d_lapps->get_directory() . '/' . $file['name'] . '/Controller.php')) {
                    // HPDL remove preg_match
                    $applications[] = $file['name'];
                }
            }
            $d_lcapps = new Directory_Listing(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . $site . '/Application');
            $d_lcapps->set_include_files(false);
            foreach ($d_lcapps->get_files() as $file) {
                if (!in_array($file['name'], $applications) && !in_array($file['name'], call_user_func(['osCommerce\OM\Core\Site\\' . $site . '\Controller', 'getGuestApplications'])) && file_exists($d_lcapps->get_directory() . '/' . $file['name'] . '/Controller.php')) {
                    $applications[] = $file['name'];
                }
            }
        }
        $shortcuts = [];
        foreach (OSCOM::call_db('GetAccessUserShortcuts', $data, 'Core') as $as) {
            $shortcuts[] = $as['module'];
        }
        $levels = [];
        foreach ($applications as $app) {
            $application_class = 'osCommerce\OM\Core\Site\\' . $site . '\Application\\' . $app . '\Controller';
            if (class_exists($application_class)) {
                if (Registry::exists('Application') && $app == OSCOM::get_site_application()) {
                    $OSCOM_Application = Registry::get('Application');
                } else {
                    Registry::get('Language')->load_ini_file($app . '.php');
                    $OSCOM_Application = new $application_class(false);
                }
                $levels[$app] = ['module' => $app, 'icon' => $OSCOM_Application->get_icon(), 'title' => $OSCOM_Application->get_title(), 'group' => $OSCOM_Application->get_group(), 'linkable' => $OSCOM_Application->can_link_to(), 'shortcut' => in_array($app, $shortcuts), 'sort_order' => $OSCOM_Application->get_sort_order()];
            }
        }
        return $levels;
    }
    public static function get_shortcuts($site = null)
    {
        if (empty($site)) {
            $site = OSCOM::get_site();
        }
        $shortcuts = [];
        if (isset($_SESSION[$site]['id'])) {
            foreach ($_SESSION[$site]['access'] as $module => $data) {
                if ($data['shortcut'] === true) {
                    $shortcuts[$module] = $data;
                }
            }
            ksort($shortcuts);
        }
        return $shortcuts;
    }
    public static function has_shortcut($site = null)
    {
        if (empty($site)) {
            $site = OSCOM::get_site();
        }
        if (isset($_SESSION[$site]['id'])) {
            foreach ($_SESSION[$site]['access'] as $module => $data) {
                if ($data['shortcut'] === true) {
                    return true;
                }
            }
        }
        return false;
    }
    public static function is_shortcut($application, $site = null)
    {
        if (empty($site)) {
            $site = OSCOM::get_site();
        }
        if (isset($_SESSION[$site]['id'])) {
            return $_SESSION[$site]['access'][$application]['shortcut'];
        }
        return false;
    }
    public static function get_levels($group = null)
    {
        $access = [];
        if (isset($_SESSION['Admin']['id']) && isset($_SESSION['Admin']['access'])) {
            foreach ($_SESSION['Admin']['access'] as $module => $data) {
                if ($data['linkable'] === true && (empty($group) || $group == $data['group'])) {
                    if (!isset($access[$data['group']][$data['sort_order']])) {
                        $access[$data['group']][$data['sort_order']] = $data;
                    } else {
                        $access[$data['group']][] = $data;
                    }
                }
            }
            ksort($access);
            foreach ($access as $group => $modules) {
                ksort($access[$group]);
            }
        }
        return $access;
    }
    public function get_module()
    {
        return $this->_module;
    }
    public static function get_group($module = null)
    {
        if (empty($module) && isset($this)) {
            // HPDL to remove
            return $this->_group;
        }
        foreach (self::get_levels() as $group => $links) {
            foreach ($links as $link) {
                if ($link['module'] == $module) {
                    return $group;
                }
            }
        }
        return false;
    }
    public static function get_group_title($group)
    {
        $OSCOM_Language = Registry::get('Language');
        if (!$OSCOM_Language->is_defined('access_group_' . $group . '_title')) {
            $OSCOM_Language->load_ini_file('modules/access/groups/' . $group . '.php');
        }
        return $OSCOM_Language->get('access_group_' . $group . '_title');
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
    public static function has_access($site, $application)
    {
        return in_array($application, call_user_func(['osCommerce\OM\Core\Site\\' . $site . '\Controller', 'getGuestApplications'])) || isset($_SESSION[$site]['access'][$application]);
    }
}