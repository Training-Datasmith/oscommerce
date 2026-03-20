<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace {
    if (!class_exists('SplClassLoader')) {
        include __DIR__ . '/../External/SplClassLoader.php';
    }
}
namespace Os_Commerce\OM\Core {
    class Autoloader extends \Spl_Class_Loader
    {
        public function load_class($class_name)
        {
            if (null === $this->_namespace || $this->_namespace . $this->_namespace_separator === substr($class_name, 0, strlen($this->_namespace . $this->_namespace_separator))) {
                $file_name = '';
                $namespace = '';
                if (false !== $last_ns_pos = strripos($class_name, $this->_namespace_separator)) {
                    $namespace = substr($class_name, 0, $last_ns_pos);
                    $class_name = substr($class_name, $last_ns_pos + 1);
                    $file_name = str_replace($this->_namespace_separator, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
                }
                $file_name .= str_replace('_', DIRECTORY_SEPARATOR, $class_name) . $this->_file_extension;
                $include_file = ($this->_include_path !== null ? $this->_include_path . DIRECTORY_SEPARATOR : '') . $file_name;
                // HPDL; require() returns a "file does not exist" error when \class_exists() is
                // used. Instead, use file_exists() and include()
                // HPDL: Check for and include custom version
                if (strpos($include_file, 'osCommerce' . DIRECTORY_SEPARATOR . 'OM' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR) !== false) {
                    $include_file = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $include_file;
                    $custom_include_file = str_replace('osCommerce' . DIRECTORY_SEPARATOR . 'OM' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR, 'osCommerce' . DIRECTORY_SEPARATOR . 'OM' . DIRECTORY_SEPARATOR . 'Custom' . DIRECTORY_SEPARATOR, $include_file);
                    if (file_exists($custom_include_file)) {
                        include $custom_include_file;
                        return true;
                    }
                }
                if (file_exists($include_file)) {
                    include $include_file;
                    return true;
                }
            }
        }
    }
}