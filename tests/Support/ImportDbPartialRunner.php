<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\OSCOM;

/**
 * Non-destructive slices of Setup importDB::execute (skips ImportSQL / FK).
 */
final class ImportDbPartialRunner
{
    public static function installServiceAndModuleStack(): void
    {
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
            'Specials',
            'Reviews',
            'RecentlyVisited',
        ];

        $installed = [];

        foreach ($services as $service) {
            $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Service\\' . $service;
            $module = new $class();
            $module->install();

            if (isset($module->depends)) {
                if (is_string($module->depends) && (($key = array_search($module->depends, $installed)) !== false)) {
                    if (isset($installed[$key + 1])) {
                        array_splice($installed, $key + 1, 0, $service);
                    } else {
                        $installed[] = $service;
                    }
                } elseif (is_array($module->depends)) {
                    $array_position = null;
                    foreach ($module->depends as $depends_module) {
                        if (($key = array_search($depends_module, $installed)) !== false) {
                            if (!isset($array_position) || ($key > $array_position)) {
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
                    $array_position = null;
                    foreach ($module->precedes as $precedes_module) {
                        if (($key = array_search($precedes_module, $installed)) !== false) {
                            if (!isset($array_position) || ($key < $array_position)) {
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

        $cfg_data = [
            'title' => 'Service Modules',
            'key' => 'MODULE_SERVICES_INSTALLED',
            'value' => implode(';', $installed),
            'description' => 'Installed services modules',
            'group_id' => '6',
        ];

        OSCOM::callDB('Admin\InsertConfigurationParameters', $cfg_data, 'Site');

        if (!\defined('DEFAULT_ORDERS_STATUS_ID')) {
            \define('DEFAULT_ORDERS_STATUS_ID', 1);
        }

        $module = new \osCommerce\OM\Core\Site\Admin\Module\Payment\COD();
        $module->install();

        OSCOM::callDB('Admin\UpdateConfigurationParameters', [
            'key' => 'MODULE_PAYMENT_COD_STATUS',
            'value' => '1',
        ], 'Site');

        $module = new \osCommerce\OM\Core\Site\Admin\Module\Shipping\Flat();
        $module->install();

        $module = new \osCommerce\OM\Core\Site\Admin\Module\OrderTotal\SubTotal();
        $module->install();

        $module = new \osCommerce\OM\Core\Site\Admin\Module\OrderTotal\Shipping();
        $module->install();

        $module = new \osCommerce\OM\Core\Site\Admin\Module\OrderTotal\Tax();
        $module->install();

        $module = new \osCommerce\OM\Core\Site\Admin\Module\OrderTotal\Total();
        $module->install();
    }
}
