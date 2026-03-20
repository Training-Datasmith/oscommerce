<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core;

define('OSCOM_BASE_DIRECTORY', realpath(__DIR__ . '/../') . '/');
class OSCOM
{
    public const TIMESTAMP_START = OSCOM_TIMESTAMP_START;
    public const BASE_DIRECTORY = OSCOM_BASE_DIRECTORY;
    protected static $_version;
    protected static $_request_type;
    protected static $_site;
    protected static $_application;
    protected static $_config;
    public static function initialize()
    {
        static::load_config();
        DateTime::set_time_zone();
        Error_Handler::initialize();
        static::set_site();
        if (!static::site_exists(static::get_site())) {
            trigger_error('Site \'' . static::get_site() . '\' does not exist', E_USER_ERROR);
            exit;
        }
        static::set_site_application();
        call_user_func(['osCommerce\OM\Core\Site\\' . static::get_site() . '\Controller', 'initialize']);
    }
    public static function site_exists($site)
    {
        return class_exists('osCommerce\OM\Core\Site\\' . $site . '\Controller');
    }
    public static function set_site($site = null)
    {
        if (isset($site)) {
            if (!static::site_exists($site)) {
                trigger_error('Site \'' . $site . '\' does not exist, using default \'' . static::get_default_site() . '\'', E_USER_ERROR);
                $site = static::get_default_site();
            }
        } else if (!empty($_GET)) {
            $requested_site = HTML::sanitize(basename(key(array_slice($_GET, 0, 1, true))));
            if (preg_match('/^[A-Z][A-Za-z0-9-_]*$/', $requested_site) && static::site_exists($requested_site)) {
                $site = $requested_site;
            } else {
                $site = static::get_default_site();
            }
        } else {
            $site = static::get_default_site();
        }
        static::$_site = $site;
    }
    public static function get_site()
    {
        return static::$_site;
    }
    public static function get_default_site()
    {
        $site = static::get_config('default_site', 'OSCOM');
        $server = HTML::sanitize($_SERVER['SERVER_NAME']);
        $sites = [];
        foreach (static::$_config as $group => $key) {
            if (isset($key['http_server']) && isset($key['https_server'])) {
                if ('http://' . $server == $key['http_server'] || 'https://' . $server == $key['https_server']) {
                    $sites[] = $group;
                }
            }
        }
        if (count($sites) > 0) {
            if (!in_array($site, $sites)) {
                $site = $sites[0];
            }
        }
        return $site;
    }
    public static function site_application_exists($application)
    {
        return class_exists('osCommerce\OM\Core\Site\\' . static::get_site() . '\Application\\' . $application . '\Controller');
    }
    public static function set_site_application($application = null)
    {
        if (isset($application)) {
            if (!static::site_application_exists($application)) {
                trigger_error('Application \'' . $application . '\' does not exist for Site \'' . static::get_site() . '\', using default \'' . static::get_default_site_application() . '\'', E_USER_ERROR);
                $application = null;
            }
        } else if (!empty($_GET)) {
            $requested_application = HTML::sanitize(basename(key(array_slice($_GET, 0, 1, true))));
            if ($requested_application == static::get_site()) {
                $requested_application = HTML::sanitize(basename(key(array_slice($_GET, 1, 1, true))));
            }
            if (!empty($requested_application) && static::site_application_exists($requested_application)) {
                $application = $requested_application;
            }
        }
        if (empty($application)) {
            $application = static::get_default_site_application();
        }
        static::$_application = $application;
    }
    public static function get_site_application()
    {
        return static::$_application;
    }
    public static function get_default_site_application()
    {
        return call_user_func(['osCommerce\OM\Core\Site\\' . static::get_site() . '\Controller', 'getDefaultApplication']);
    }
    public static function load_config()
    {
        $ini = parse_ini_file(static::BASE_DIRECTORY . 'Config/settings.ini', true);
        if (file_exists(static::BASE_DIRECTORY . 'Config/local_settings.ini')) {
            $local = parse_ini_file(static::BASE_DIRECTORY . 'Config/local_settings.ini', true);
            $ini = array_merge($ini, $local);
        }
        static::$_config = $ini;
    }
    public static function get_config($key, $group = null)
    {
        if (!isset($group)) {
            $group = static::get_site();
        }
        return static::$_config[$group][$key];
    }
    public static function config_exists($key, $group = null)
    {
        if (!isset($group)) {
            $group = static::get_site();
        }
        return isset(static::$_config[$group][$key]);
    }
    public static function set_config($key, $value, $group = null)
    {
        if (!isset($group)) {
            $group = static::get_site();
        }
        static::$_config[$group][$key] = $value;
    }
    public static function get_version()
    {
        if (!isset(static::$_version)) {
            $v = trim(file_get_contents(static::BASE_DIRECTORY . 'version.txt'));
            if (preg_match('/^(\d+\.)?(\d+\.)?(\d+)$/', $v)) {
                static::$_version = $v;
            } else {
                trigger_error('Version number is not numeric. Please verify: ' . static::BASE_DIRECTORY . 'version.txt');
            }
        }
        return static::$_version;
    }
    protected static function set_request_type()
    {
        static::$_request_type = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'SSL' : 'NONSSL';
    }
    public static function get_request_type()
    {
        if (!isset(static::$_request_type)) {
            static::set_request_type();
        }
        return static::$_request_type;
    }
    /**
     * Return an internal URL address.
     *
     * @param string $site The Site to link to. Default: The currently used Site.
     * @param string $application The Site Application to link to. Default: The currently used Site Application.
     * @param string $parameters Parameters to add to the link. Example: key1=value1&key2=value2
     * @param string $connection The type of connection to use for the link. Values: NONSSL, SSL, AUTO. Default: NONSSL.
     * @param bool $add_session_id Add the session ID to the link. Default: True.
     * @param bool $search_engine_safe Use search engine safe URLs. Default: True.
     * @return string The URL address.
     */
    public static function get_link($site = null, $application = null, $parameters = null, $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true)
    {
        if (empty($site)) {
            $site = static::get_site();
        }
        if (empty($application) && $site == static::get_site()) {
            $application = static::get_site_application();
        }
        if (!in_array($connection, ['NONSSL', 'SSL', 'AUTO'])) {
            $connection = 'NONSSL';
        }
        if (!is_bool($add_session_id)) {
            $add_session_id = true;
        }
        if (!is_bool($search_engine_safe)) {
            $search_engine_safe = true;
        }
        // Wrapper for RPC links; RPC cannot perform cross domain requests
        $real_site = $site == 'RPC' ? $application : $site;
        if ($connection == 'AUTO') {
            if (static::get_request_type() == 'SSL' && static::get_config('enable_ssl', $real_site) == 'true') {
                $link = static::get_config('https_server', $real_site) . static::get_config('dir_ws_https_server', $real_site);
            } else {
                $link = static::get_config('http_server', $real_site) . static::get_config('dir_ws_http_server', $real_site);
            }
        } elseif ($connection == 'SSL' && static::get_config('enable_ssl', $real_site) == 'true') {
            $link = static::get_config('https_server', $real_site) . static::get_config('dir_ws_https_server', $real_site);
        } else {
            $link = static::get_config('http_server', $real_site) . static::get_config('dir_ws_http_server', $real_site);
        }
        $link .= static::get_config('bootstrap_file', 'OSCOM') . '?';
        if ($site != static::get_default_site()) {
            $link .= $site . '&';
        }
        if (!empty($application) && $application != static::get_default_site_application()) {
            $link .= $application . '&';
        }
        if (!empty($parameters)) {
            $link .= HTML::output($parameters) . '&';
        }
        if ($add_session_id === true && Registry::exists('Session') && Registry::get('Session')->has_started() && SERVICE_SESSION_FORCE_COOKIE_USAGE == '-1') {
            if (strlen(SID) > 0) {
                $_sid = SID;
            } elseif (static::get_request_type() == 'NONSSL' && $connection == 'SSL' && static::get_config('enable_ssl', $site) == 'true' || static::get_request_type() == 'SSL' && $connection != 'SSL') {
                if (static::get_config('http_cookie_domain', $site) != static::get_config('https_cookie_domain', $site)) {
                    $_sid = Registry::get('Session')->get_name() . '=' . Registry::get('Session')->get_id();
                }
            }
        }
        if (isset($_sid)) {
            $link .= HTML::output($_sid);
        }
        while (substr($link, -1) == '&' || substr($link, -1) == '?') {
            $link = substr($link, 0, -1);
        }
        if ($search_engine_safe === true && Registry::exists('osC_Services') && Registry::get('osC_Services')->is_started('sefu')) {
            $link = str_replace(['?', '&', '='], ['/', '/', ','], $link);
        }
        return $link;
    }
    /**
     * Return an internal URL address for public objects.
     *
     * @param string $url The object location from the public/sites/SITE/ directory.
     * @param string $parameters Parameters to add to the link. Example: key1=value1&key2=value2
     * @param string $site Get a public link from a specific Site
     * @return string The URL address.
     */
    public static function get_public_site_link($url, $parameters = null, $site = null)
    {
        if (!isset($site)) {
            $site = static::get_site();
        }
        $link = 'public/sites/' . $site . '/' . $url;
        if (!empty($parameters)) {
            $link .= '?' . HTML::output($parameters);
        }
        while (substr($link, -1) == '&' || substr($link, -1) == '?') {
            $link = substr($link, 0, -1);
        }
        return $link;
    }
    /**
     * Return an internal URL address for an RPC call.
     *
     * @param string $site The Site to link to. Default: The currently used Site.
     * @param string $application The Site Application to link to. Default: The currently used Site Application.
     * @param string $parameters Parameters to add to the link. Example: key1=value1&key2=value2
     * @param string $connection The type of connection to use for the link. Values: NONSSL, SSL, AUTO. Default: NONSSL.
     * @param bool $add_session_id Add the session ID to the link. Default: True.
     * @param bool $search_engine_safe Use search engine safe URLs. Default: True.
     * @return string The URL address.
     */
    public static function get_rpc_link($site = null, $application = null, $parameters = null, $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true)
    {
        if (empty($site)) {
            $site = static::get_site();
        }
        if (empty($application)) {
            $application = static::get_site_application();
        }
        return static::get_link('RPC', $site, $application . '&' . $parameters, $connection, $add_session_id, $search_engine_safe);
    }
    public static function redirect($url)
    {
        if (strpos($url, "\n") !== false || strpos($url, "\r") !== false) {
            $url = static::get_link(OSCOM::get_default_site());
        }
        if (strpos($url, '&amp;') !== false) {
            $url = str_replace('&amp;', '&', $url);
        }
        header('Location: ' . $url);
        exit;
    }
    /**
     * Return a language definition
     *
     * @param string $key The language definition to return
     * @return string The language definition
     */
    public static function get_def($key)
    {
        return Registry::get('Language')->get($key);
    }
    /**
     * Execute database queries
     *
     * @param string $procedure The name of the database query to execute
     * @param array $data Parameters passed to the database query
     * @param string $type The namespace type the database query is stored in [ Core, Site, CoreUpdate (@since v3.0.2), Application (default) ]
     * @return mixed The result of the database query
     */
    public static function call_db($procedure, $data = null, $type = 'Application')
    {
        $OSCOM_PDO = Registry::get('PDO');
        $call = explode('\\', $procedure);
        switch ($type) {
            case 'Core':
                $procedure = array_pop($call);
                $ns = 'osCommerce\OM\Core';
                if (!empty($call)) {
                    $ns .= '\\' . implode('\\', $call);
                }
                break;
            case 'Site':
                $ns = 'osCommerce\OM\Core\Site\\' . $call[0];
                $procedure = $call[1];
                break;
            case 'CoreUpdate':
                $ns = 'osCommerce\OM\Work\CoreUpdate\\' . $call[0];
                $procedure = $call[1];
                break;
            case 'Application':
            default:
                $ns = 'osCommerce\OM\Core\Site\\' . $call[0] . '\Application\\' . $call[1];
                $procedure = $call[2];
        }
        $db_driver = $OSCOM_PDO->get_driver();
        if (!class_exists($ns . '\SQL\\' . $db_driver . '\\' . $procedure)) {
            if ($OSCOM_PDO->has_driver_parent() && class_exists($ns . '\SQL\\' . $OSCOM_PDO->get_driver_parent() . '\\' . $procedure)) {
                $db_driver = $OSCOM_PDO->get_driver_parent();
            } else {
                $db_driver = 'SqlBuilder';
            }
        }
        return call_user_func([$ns . '\SQL\\' . $db_driver . '\\' . $procedure, 'execute'], $data);
    }
    /**
     * Set a cookie
     *
     * @param string $name The name of the cookie
     * @param string $value The value of the cookie
     * @param int $expire Unix timestamp of when the cookie should expire
     * @param string $path The path on the server for which the cookie will be available on
     * @param string $domain The The domain that the cookie is available on
     * @param boolean $secure Indicates whether the cookie should only be sent over a secure HTTPS connection
     * @param boolean $httpOnly Indicates whether the cookie should only accessible over the HTTP protocol
     * @return boolean
     * @since v3.0.0
     */
    public static function set_cookie($name, $value = null, $expires = 0, $path = null, $domain = null, $secure = false, $http_only = false)
    {
        if (!isset($path)) {
            $path = static::get_request_type() == 'NONSSL' ? static::get_config('http_cookie_path') : static::get_config('https_cookie_path');
        }
        if (!isset($domain)) {
            $domain = static::get_request_type() == 'NONSSL' ? static::get_config('http_cookie_domain') : static::get_config('https_cookie_domain');
        }
        return setcookie($name, $value, $expires, $path, $domain, $secure, $http_only);
    }
    /**
     * Get the IP address of the client
     *
     * @since v3.0.0
     */
    public static function get_ip_address()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    /**
     * Get all parameters in the GET scope
     *
     * @param array $exclude A list of parameters to exclude
     * @return string
     * @since v3.0.0
     */
    public static function get_all_get($exclude = null)
    {
        if (!is_array($exclude)) {
            if (!empty($exclude)) {
                $exclude = [$exclude];
            } else {
                $exclude = [];
            }
        }
        $params = '';
        $array = [static::get_site(), static::get_site_application(), Registry::get('Session')->get_name(), 'error', 'x', 'y'];
        $exclude = array_merge($exclude, $array);
        foreach ($_GET as $key => $value) {
            if (!in_array($key, $exclude)) {
                $params .= $key . (!empty($value) ? '=' . $value : '') . '&';
            }
        }
        if (!empty($params)) {
            $params = substr($params, 0, -1);
        }
        return $params;
    }
}