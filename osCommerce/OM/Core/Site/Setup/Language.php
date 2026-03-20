<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Setup;

use Os_Commerce\OM\Core\Directory_Listing;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\XML;
class Language extends \Os_Commerce\OM\Core\Site\Admin\Language
{
    public function __construct()
    {
        $d_llang = new Directory_Listing(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages');
        $d_llang->set_include_directories(false);
        $d_llang->set_check_extension('xml');
        foreach ($d_llang->get_files() as $file) {
            $lang = XML::to_array(simplexml_load_file(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages/' . $file['name']));
            if (!isset($lang['language'])) {
                // create root element (simpleXML does not use root element)
                $lang = ['language' => $lang];
            }
            $this->_languages[$lang['language']['data']['code']] = [
                'id' => 1,
                //HPDL to remove
                'code' => $lang['language']['data']['code'],
                'name' => $lang['language']['data']['title'],
                'locale' => $lang['language']['data']['locale'],
                'charset' => $lang['language']['data']['character_set'],
                'date_format_short' => $lang['language']['data']['date_format_short'],
                'date_format_long' => $lang['language']['data']['date_format_long'],
                'time_format' => $lang['language']['data']['time_format'],
                'text_direction' => $lang['language']['data']['text_direction'],
                'parent_id' => 0,
            ];
        }
        unset($lang);
        $language = isset($_GET['language']) && !empty($_GET['language']) ? $_GET['language'] : '';
        $this->set($language);
        header('Content-Type: text/html; charset=' . $this->get_character_set());
        setlocale(LC_TIME, explode(',', $this->get_locale()));
        $this->load_ini_file();
        $this->load_ini_file(OSCOM::get_site_application() . '.php');
    }
    public function set($code = null)
    {
        $this->_code = $code;
        if (empty($this->_code)) {
            if (isset($_COOKIE[OSCOM::get_site()]['language'])) {
                $this->_code = $_COOKIE[OSCOM::get_site()]['language'];
            } else {
                $this->_code = $this->get_browser_setting();
            }
        }
        if (empty($this->_code) || !$this->exists($this->_code)) {
            $this->_code = 'en_US';
        }
        if (!isset($_COOKIE[OSCOM::get_site()]['language']) || $_COOKIE[OSCOM::get_site()]['language'] != $this->_code) {
            OSCOM::set_cookie(OSCOM::get_site() . '[language]', $this->_code, time() + 60 * 60 * 24 * 90);
        }
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
            if (file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::get_site() . '/Languages/' . $language_code . '.php')) {
                $contents = file(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::get_site() . '/Languages/' . $language_code . '.php');
            } else {
                return [];
            }
        } else {
            if (substr(realpath(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::get_site() . '/Languages/' . $language_code . '/' . $filename), 0, strlen(realpath(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::get_site() . '/Languages/' . $language_code))) != realpath(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::get_site() . '/Languages/' . $language_code)) {
                return [];
            }
            if (!file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::get_site() . '/Languages/' . $language_code . '/' . $filename)) {
                return [];
            }
            $contents = file(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::get_site() . '/Languages/' . $language_code . '/' . $filename);
        }
        $ini_array = [];
        foreach ($contents as $line) {
            $line = trim($line);
            $firstchar = substr($line, 0, 1);
            if (!empty($line) && $firstchar != $comment) {
                $delimiter = strpos($line, '=');
                if ($delimiter !== false) {
                    $key = trim(substr($line, 0, $delimiter));
                    $value = trim(substr($line, $delimiter + 1));
                    $ini_array[$key] = $value;
                } elseif (isset($key)) {
                    $ini_array[$key] .= trim($line);
                }
            }
        }
        unset($contents);
        $this->_definitions = array_merge($this->_definitions, $ini_array);
    }
    public function get_code($id = null)
    {
        return $this->_code;
    }
}