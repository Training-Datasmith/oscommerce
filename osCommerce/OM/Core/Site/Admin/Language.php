<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin;

use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\XML;
class Language extends \Os_Commerce\OM\Core\Language
{
    public function __construct()
    {
        parent::__construct();
        header('Content-Type: text/html; charset=' . $this->get_character_set());
        setlocale(LC_TIME, explode(',', $this->get_locale()));
        $this->load_ini_file();
        $this->load_ini_file(OSCOM::get_site_application() . '.php');
    }
    public function load_ini_file($filename = null, $comment = '#', $language_code = null)
    {
        if (is_null($language_code)) {
            $language_code = $this->_code;
        }
        if ($this->_languages[$language_code]['parent_id'] > 0) {
            $this->load_ini_file($filename, $comment, $this->get_code_from_id($this->_languages[$language_code]['parent_id']));
        }
        if (is_null($filename)) {
            if (file_exists(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::get_site() . '/languages/' . $language_code . '.php')) {
                $contents = file(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::get_site() . '/languages/' . $language_code . '.php');
            } elseif (file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::get_site() . '/languages/' . $language_code . '.php')) {
                $contents = file(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::get_site() . '/languages/' . $language_code . '.php');
            } else {
                return [];
            }
        } else {
            if (substr(realpath(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::get_site() . '/languages/' . $language_code . '/' . $filename), 0, strlen(realpath(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::get_site_application() . '/languages/' . $language_code))) != realpath(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::get_site_application() . '/languages/' . $language_code)) {
                return [];
            }
            if (substr(realpath(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::get_site() . '/languages/' . $language_code . '/' . $filename), 0, strlen(realpath(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::get_site_application() . '/languages/' . $language_code))) != realpath(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::get_site_application() . '/languages/' . $language_code)) {
                return [];
            }
            if (file_exists(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::get_site() . '/languages/' . $language_code . '/' . $filename)) {
                $contents = file(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::get_site() . '/languages/' . $language_code . '/' . $filename);
            } elseif (file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::get_site() . '/languages/' . $language_code . '/' . $filename)) {
                $contents = file(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::get_site() . '/languages/' . $language_code . '/' . $filename);
            } else {
                return [];
            }
        }
        $ini_array = [];
        foreach ($contents as $line) {
            $line = trim($line);
            $firstchar = substr($line, 0, 1);
            if (!empty($line) && $firstchar != $comment) {
                $delimiter = strpos($line, '=');
                if ($delimiter !== false && substr_count(substr($line, 0, $delimiter), ' ') == 1) {
                    $key = trim(substr($line, 0, $delimiter));
                    $value = trim(substr($line, $delimiter + 1));
                    $ini_array[$key] = $value;
                } elseif (isset($key)) {
                    $ini_array[$key] .= "\n" . trim($line);
                }
            }
        }
        unset($contents);
        $this->_definitions = array_merge($this->_definitions, $ini_array);
    }
    public function inject_definitions($file, $language_code = null)
    {
        if (is_null($language_code)) {
            $language_code = $this->_code;
        }
        if ($this->_languages[$language_code]['parent_id'] > 0) {
            $this->inject_definitions($file, $this->get_code_from_id($this->_languages[$language_code]['parent_id']));
        }
        foreach ($this->extract_definitions($language_code . '/' . $file) as $def) {
            $this->_definitions[$def['key']] = $def['value'];
        }
    }
    public static function extract_definitions($xml)
    {
        $definitions = [];
        if (file_exists(OSCOM::BASE_DIRECTORY . 'Custom/Site/Shop/Languages/' . $xml)) {
            $definitions = XML::to_array(simplexml_load_file(OSCOM::BASE_DIRECTORY . 'Custom/Site/Shop/Languages/' . $xml));
        } elseif (file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages/' . $xml)) {
            $definitions = XML::to_array(simplexml_load_file(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages/' . $xml));
        }
        if (!empty($definitions)) {
            if (!isset($definitions['language'])) {
                // create root element (simpleXML does not use root element)
                $definitions = ['language' => $definitions];
            }
            if (!isset($definitions['language']['definitions']['definition'][0])) {
                $definitions['language']['definitions']['definition'] = [$definitions['language']['definitions']['definition']];
            }
            $definitions = $definitions['language']['definitions']['definition'];
        }
        return $definitions;
    }
    public function get_data($id, $key = null)
    {
        $data = ['id' => $id];
        $result = OSCOM::call_db('Admin\GetLanguage', $data, 'Site');
        if (empty($key)) {
            return $result;
        } else {
            return $result[$key];
        }
    }
    public function get_id($code = null)
    {
        if (empty($code)) {
            return $this->_languages[$this->_code]['id'];
        }
        $data = ['code' => $code];
        $result = OSCOM::call_db('Admin\GetLanguageID', $data, 'Site');
        return $result['languages_id'];
    }
    public function get_code($id = null)
    {
        if (empty($id)) {
            return $this->_code;
        }
        return $this->get_data($id, 'code');
    }
    public function is_defined($key)
    {
        return isset($this->_definitions[$key]);
    }
}