<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Setup\Application\Install\Model;

use Os_Commerce\OM\Core\Directory_Listing;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\PDO;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Setup\Language;
class Import_Db
{
    public static function execute($data)
    {
        Registry::set('PDO', PDO::initialize($data['server'], $data['username'], $data['password'], $data['database'], $data['port'], $data['class']));
        // Import SQL queries
        OSCOM::call_db('Setup\Install\ImportSQL', ['table_prefix' => $data['table_prefix']]);
        // Import language definitions
        OSCOM::set_config('db_table_prefix', $data['table_prefix'], 'Admin');
        OSCOM::set_config('db_table_prefix', $data['table_prefix'], 'Shop');
        OSCOM::set_config('db_table_prefix', $data['table_prefix'], 'Setup');
        foreach (Language::extract_definitions('en_US.xml') as $def) {
            $def['id'] = 1;
            OSCOM::call_db('Admin\InsertLanguageDefinition', $def, 'Site');
        }
        $DL_lang = new Directory_Listing(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages/en_US');
        $DL_lang->set_recursive(true);
        $DL_lang->set_include_directories(false);
        $DL_lang->set_add_directory_to_filename(true);
        $DL_lang->set_check_extension('xml');
        foreach ($DL_lang->get_files() as $files) {
            foreach (Language::extract_definitions('en_US/' . $files['name']) as $def) {
                $def['id'] = 1;
                OSCOM::call_db('Admin\InsertLanguageDefinition', $def, 'Site');
            }
        }
        // Import Service modules
        $services = [
            'OutputCompression',
            'Session',
            'Language',
            'Debug',
            'Currencies',
            'Core',
            'SimpleCounter',
            'CategoryPath',
            'Breadcrumb',
            'WhosOnline',
            // HPDL                   'banner',
            'Specials',
            'Reviews',
            'RecentlyVisited',
        ];
        $installed = [];
        foreach ($services as $service) {
            $class = 'osCommerce\OM\Core\Site\Admin\Module\Service\\' . $service;
            $module = new $class();
            $module->install();
            if (isset($module->depends)) {
                if (is_string($module->depends) && ($key = array_search($module->depends, $installed)) !== false) {
                    if (isset($installed[$key + 1])) {
                        array_splice($installed, $key + 1, 0, $service);
                    } else {
                        $installed[] = $service;
                    }
                } elseif (is_array($module->depends)) {
                    foreach ($module->depends as $depends_module) {
                        if (($key = array_search($depends_module, $installed)) !== false) {
                            if (!isset($array_position) || $key > $array_position) {
                                $array_position = $key;
                            }
                        }
                    }
                    if (isset($array_position)) {
                        array_splice($installed, $array_position + 1, 0, $service);
                    } else {
                        $installed[] = $service;
                    }
                }
            } elseif (isset($module->precedes)) {
                if (is_string($module->precedes)) {
                    if (($key = array_search($module->precedes, $installed)) !== false) {
                        array_splice($installed, $key, 0, $service);
                    } else {
                        $installed[] = $service;
                    }
                } elseif (is_array($module->precedes)) {
                    foreach ($module->precedes as $precedes_module) {
                        if (($key = array_search($precedes_module, $installed)) !== false) {
                            if (!isset($array_position) || $key < $array_position) {
                                $array_position = $key;
                            }
                        }
                    }
                    if (isset($array_position)) {
                        array_splice($installed, $array_position, 0, $service);
                    } else {
                        $installed[] = $service;
                    }
                }
            } else {
                $installed[] = $service;
            }
            unset($array_position);
        }
        $cfg_data = ['title' => 'Service Modules', 'key' => 'MODULE_SERVICES_INSTALLED', 'value' => implode(';', $installed), 'description' => 'Installed services modules', 'group_id' => '6'];
        OSCOM::call_db('Admin\InsertConfigurationParameters', $cfg_data, 'Site');
        // Import Payment modules
        define('DEFAULT_ORDERS_STATUS_ID', 1);
        $module = new \Os_Commerce\OM\Core\Site\Admin\Module\Payment\COD();
        $module->install();
        $pm_data = ['key' => 'MODULE_PAYMENT_COD_STATUS', 'value' => '1'];
        OSCOM::call_db('Admin\UpdateConfigurationParameters', $pm_data, 'Site');
        // Import Shipping modules
        $module = new \Os_Commerce\OM\Core\Site\Admin\Module\Shipping\Flat();
        $module->install();
        // Import Order Total modules
        $module = new \Os_Commerce\OM\Core\Site\Admin\Module\Order_Total\Sub_Total();
        $module->install();
        $module = new \Os_Commerce\OM\Core\Site\Admin\Module\Order_Total\Shipping();
        $module->install();
        $module = new \Os_Commerce\OM\Core\Site\Admin\Module\Order_Total\Tax();
        $module->install();
        $module = new \Os_Commerce\OM\Core\Site\Admin\Module\Order_Total\Total();
        $module->install();
        // Import Foreign Keys
        OSCOM::call_db('Setup\Install\ImportFK', ['table_prefix' => $data['table_prefix']]);
    }
}