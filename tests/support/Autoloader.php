<?php

declare(strict_types=1);

/**
 * Test autoloader: loads OM classes and prefers test stubs under tests/stubs/.
 */

if (!class_exists('SplClassLoader')) {
    include __DIR__ . '/../../osCommerce/OM/External/SplClassLoader.php';
}

class Autoloader extends SplClassLoader
{
    public function loadClass($className)
    {
        if (null === $this->_namespace || $this->_namespace . $this->_namespaceSeparator === substr($className, 0, strlen($this->_namespace . $this->_namespaceSeparator))) {
            $fileName = '';
            $namespace = '';

            if (false !== ($lastNsPos = strripos($className, $this->_namespaceSeparator))) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName = str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }

            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . $this->_fileExtension;

            $includeFile = ($this->_includePath !== null ? $this->_includePath . DIRECTORY_SEPARATOR : '') . $fileName;

            if (strpos($includeFile, 'osCommerce' . DIRECTORY_SEPARATOR . 'OM' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR) !== false) {
                $includeFile = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . $includeFile;

                $custom_includeFile = str_replace(
                    'osCommerce' . DIRECTORY_SEPARATOR . 'OM' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR,
                    'tests' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'osCommerce' . DIRECTORY_SEPARATOR . 'OM' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR,
                    $includeFile
                );

                if (file_exists($custom_includeFile)) {
                    include $custom_includeFile;

                    return true;
                }
            }

            if (file_exists($includeFile)) {
                include $includeFile;

                return true;
            }
        }
    }
}
