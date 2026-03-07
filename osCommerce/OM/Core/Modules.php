<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core;

class Modules
{
    public $_modules;
    public $_code;
    public $_title;
    public $_title_link;
    public $_content;
    public $_author_name;
    public $_author_www;
    public $_keys;
    public $_group;

    public function __construct($group)
    {
        $OSCOM_Cache = Registry::get('Cache');
        $OSCOM_Template = Registry::get('Template');
        $OSCOM_PDO = Registry::get('PDO');

        $this->_group = $group;

        if ($OSCOM_Cache->read('templates_' . $this->_group . '_layout-' . $OSCOM_Template->getCode() . '-' . OSCOM::getSiteApplication() . '-' . $OSCOM_Template->getPageContentsFilename())) {
            $data = $OSCOM_Cache->getCache();
        } else {
            $data = [];

            $Qspecific = $OSCOM_PDO->prepare('select b2p.boxes_group, b.code from :table_templates_boxes_to_pages b2p, :table_templates_boxes b, :table_templates t where b2p.templates_id = :templates_id and b2p.page_specific = 1 and b2p.content_page in ("*", "' . OSCOM::getSiteApplication() . '/*", "' . OSCOM::getSiteApplication() . '/' . substr($OSCOM_Template->getPageContentsFilename(), 0, strrpos($OSCOM_Template->getPageContentsFilename(), '.')) . '") and b2p.templates_boxes_id = b.id and b.modules_group = :modules_group and b2p.templates_id = t.id order by b2p.boxes_group, b2p.sort_order');
            $Qspecific->bindInt(':templates_id', $OSCOM_Template->getID());
            $Qspecific->bindValue(':modules_group', $this->_group);
            $Qspecific->execute();

            $result = $Qspecific->fetchAll();

            if (count($result) > 0) {
                foreach ($result as $r) {
                    $data[$r['boxes_group']][] = $r['code'];
                }
            } else {
                $_data = [];

                $Qmodules = $OSCOM_PDO->prepare('select b2p.boxes_group, b2p.content_page, b.code from :table_templates_boxes_to_pages b2p, :table_templates_boxes b, :table_templates t where b2p.templates_id = :templates_id and b2p.content_page in ("*", "' . OSCOM::getSiteApplication() . '/*", "' . OSCOM::getSiteApplication() . '/' . substr($OSCOM_Template->getPageContentsFilename(), 0, strrpos($OSCOM_Template->getPageContentsFilename(), '.')) . '") and b2p.templates_boxes_id = b.id and b.modules_group = :modules_group and b2p.templates_id = t.id order by b2p.boxes_group, b2p.sort_order');
                $Qmodules->bindInt(':templates_id', $OSCOM_Template->getID());
                $Qmodules->bindValue(':modules_group', $this->_group);
                $Qmodules->execute();

                while ($Qmodules->fetch()) {
                    $_data[$Qmodules->value('boxes_group')][] = ['code' => $Qmodules->value('code'),
                                                                      'page' => $Qmodules->value('content_page')];
                }

                foreach ($_data as $groups => $modules) {
                    $clean = [];

                    foreach ($modules as $module) {
                        if (isset($clean[$module['code']])) {
                            if (substr_count($module['page'], '/') > substr_count($clean[$module['code']]['page'], '/')) {
                                unset($clean[$module['code']]);
                            }
                        }

                        $clean[$module['code']] = $module;
                    }

                    $_data[$groups] = $clean;
                }

                foreach ($_data as $groups => $modules) {
                    foreach ($modules as $module) {
                        $data[$groups][] = $module['code'];
                    }
                }
            }

            $OSCOM_Cache->write($data);
        }

        $this->_modules = $data;
    }

    public function getCode()
    {
        return $this->_code;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function getTitleLink()
    {
        return $this->_title_link;
    }

    public function hasTitleLink()
    {
        return !empty($this->_title_link);
    }

    public function getContent()
    {
        return $this->_content;
    }

    public function hasContent()
    {
        return !empty($this->_content);
    }

    public function getAuthorName()
    {
        return $this->_author_name;
    }

    public function getAuthorAddress()
    {
        return $this->_author_www;
    }

    public function getGroup($group)
    {
        $modules = [];

        if (isset($this->_modules[$group])) {
            foreach ($this->_modules[$group] as $module) {
                $class = 'osCommerce\\OM\\Core\\Site\\Shop\\Module\\' . $this->_group . '\\' . $module . '\\Controller';

                if (class_exists($class)) {
                    $modules[] = $class;
                } else {
                    trigger_error($class . ' not found');
                }
            }
        }

        return $modules;
    }

    public function isInstalled($code = '', $group = '')
    {
        $OSCOM_PDO = Registry::get('PDO');

        if (empty($code) && empty($group)) {
            static $is_installed;

            $code = $this->_code;
            $group = $this->_group;
        }

        if (!isset($is_installed)) {
            $Qcheck = $OSCOM_PDO->query('select id from :table_templates_boxes where code = :code and modules_group = :modules_group');
            $Qcheck->bindValue(':code', $code);
            $Qcheck->bindValue(':modules_group', $group);
            $Qcheck->execute();

            $result = $Qcheck->fetch();

            $is_installed = (count($result) > 0) ? true : false;
        }

        return $is_installed;
    }

    public function hasKeys()
    {
        static $has_keys;

        if (isset($has_keys) === false) {
            $has_keys = (sizeof($this->getKeys()) > 0) ? true : false;
        }

        return $has_keys;
    }

    public function getKeys()
    {
        if (isset($this->_keys) === false) {
            $this->_keys = [];
        }

        return $this->_keys;
    }

    public function isActive()
    {
        return true;
    }

    public function install()
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Language = Registry::get('Language');

        $Qinstall = $OSCOM_PDO->prepare('insert into :table_templates_boxes (title, code, author_name, author_www, modules_group) values (:title, :code, :author_name, :author_www, :modules_group)');
        $Qinstall->bindValue(':title', $this->_title);
        $Qinstall->bindValue(':code', $this->_code);
        $Qinstall->bindValue(':author_name', $this->_author_name);
        $Qinstall->bindValue(':author_www', $this->_author_www);
        $Qinstall->bindValue(':modules_group', $this->_group);
        $Qinstall->execute();

        foreach ($OSCOM_Language->getAll() as $key => $value) {
            if (file_exists(dirname(__FILE__) . '/../languages/' . $key . '/modules/' . $this->_group . '/' . $this->_code . '.xml')) {
                foreach ($OSCOM_Language->extractDefinitions($key . '/modules/' . $this->_group . '/' . $this->_code . '.xml') as $def) {
                    $Qcheck = $OSCOM_PDO->prepare('select id from :table_languages_definitions where definition_key = :definition_key and content_group = :content_group and languages_id = :languages_id limit 1');
                    $Qcheck->bindValue(':definition_key', $def['key']);
                    $Qcheck->bindValue(':content_group', $def['group']);
                    $Qcheck->bindInt(':languages_id', $value['id']);
                    $Qcheck->execute();

                    $result = $Qcheck->fetch();

                    if (count($result) === 1) {
                        $Qdef = $OSCOM_PDO->prepare('update :table_languages_definitions set definition_value = :definition_value where definition_key = :definition_key and content_group = :content_group and languages_id = :languages_id');
                    } else {
                        $Qdef = $OSCOM_PDO->prepare('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
                    }
                    $Qdef->bindInt(':languages_id', $value['id']);
                    $Qdef->bindValue(':content_group', $def['group']);
                    $Qdef->bindValue(':definition_key', $def['key']);
                    $Qdef->bindValue(':definition_value', $def['value']);
                    $Qdef->execute();
                }
            }
        }

        Cache::clear('languages');
    }

    public function remove()
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Language = Registry::get('Language');

        $Qdel = $OSCOM_PDO->prepare('delete from :table_templates_boxes where code = :code and modules_group = :modules_group');
        $Qdel->bindValue(':code', $this->_code);
        $Qdel->bindValue(':modules_group', $this->_group);
        $Qdel->execute();

        if ($this->hasKeys()) {
            $OSCOM_PDO->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->getKeys()) . '")');
        }

        if (file_exists(dirname(__FILE__) . '/../languages/' . $OSCOM_Language->getCode() . '/modules/' . $this->_group . '/' . $this->_code . '.xml')) {
            foreach ($OSCOM_Language->extractDefinitions($OSCOM_Language->getCode() . '/modules/' . $this->_group . '/' . $this->_code . '.xml') as $def) {
                $Qdel = $OSCOM_PDO->prepare('delete from :table_languages_definitions where definition_key = :definition_key and content_group = :content_group');
                $Qdel->bindValue(':definition_key', $def['key']);
                $Qdel->bindValue(':content_group', $def['group']);
                $Qdel->execute();
            }

            Cache::clear('languages');
        }
    }
}
