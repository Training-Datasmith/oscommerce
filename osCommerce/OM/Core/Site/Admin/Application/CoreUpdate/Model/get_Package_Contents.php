<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Core_Update\Model;

use Os_Commerce\OM\Core\Directory_Listing;
use Os_Commerce\OM\Core\OSCOM;
use Phar;
use Recursive_Iterator_Iterator;
class Get_Package_Contents
{
    public static function execute()
    {
        $result = ['entries' => []];
        $phar_can_open = true;
        try {
            $phar = new Phar(OSCOM::BASE_DIRECTORY . 'Work/CoreUpdate/update.phar');
        } catch (\Exception $e) {
            $phar_can_open = false;
            trigger_error($e->get_message());
        }
        if ($phar_can_open === true) {
            $update_pkg = [];
            foreach (new Recursive_Iterator_Iterator($phar) as $iteration) {
                if (($pos = strpos($iteration->get_path_name(), 'update.phar')) !== false) {
                    $update_pkg[] = substr($iteration->get_path_name(), $pos + 12);
                }
            }
            natcasesort($update_pkg);
            $counter = 0;
            foreach ($update_pkg as $file) {
                if (substr($file, 0, 14) == 'osCommerce/OM/') {
                    $custom = false;
                    if (substr($file, 14, 5) == 'Core/') {
                        $custom = file_exists(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/osCommerce/OM/Custom/' . substr($file, 19));
                    }
                    $result['entries'][] = ['key' => $counter, 'name' => $file, 'exists' => file_exists(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file), 'writable' => self::is_writable(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file) && self::is_writable(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . dirname($file)), 'custom' => $custom, 'to_delete' => false];
                    $counter++;
                } elseif (substr($file, 0, 7) == 'public/') {
                    $result['entries'][] = ['key' => $counter, 'name' => $file, 'exists' => file_exists(realpath(OSCOM::get_config('dir_fs_public', 'OSCOM') . '../') . '/' . $file), 'writable' => self::is_writable(realpath(OSCOM::get_config('dir_fs_public', 'OSCOM') . '../') . '/' . $file) && self::is_writable(realpath(OSCOM::get_config('dir_fs_public', 'OSCOM') . '../') . '/' . dirname($file)), 'custom' => false, 'to_delete' => false];
                    $counter++;
                }
            }
        }
        $meta = $phar->get_metadata();
        if (isset($meta['delete'])) {
            $files = [];
            foreach ($meta['delete'] as $file) {
                if (substr($file, 0, 14) == 'osCommerce/OM/') {
                    if (file_exists(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file)) {
                        if (is_dir(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file)) {
                            $DL = new Directory_Listing(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file);
                            $DL->set_recursive(true);
                            $DL->set_add_directory_to_filename(true);
                            $DL->set_include_directories(false);
                            foreach ($DL->get_files() as $f) {
                                $files[] = $file . '/' . $f['name'];
                            }
                        } else {
                            $files[] = $file;
                        }
                    }
                } elseif (substr($file, 0, 7) == 'public/') {
                    if (file_exists(realpath(OSCOM::get_config('dir_fs_public', 'OSCOM') . '../') . '/' . $file)) {
                        if (is_dir(realpath(OSCOM::get_config('dir_fs_public', 'OSCOM') . '../') . '/' . $file)) {
                            $DL = new Directory_Listing(realpath(OSCOM::get_config('dir_fs_public', 'OSCOM') . '../') . '/' . $file);
                            $DL->set_recursive(true);
                            $DL->set_add_directory_to_filename(true);
                            $DL->set_include_directories(false);
                            foreach ($DL->get_files() as $f) {
                                $files[] = $file . '/' . $f['name'];
                            }
                        } else {
                            $files[] = $file;
                        }
                    }
                }
            }
            natcasesort($files);
            foreach ($files as $d) {
                $writable = false;
                $custom = false;
                if (substr($d, 0, 14) == 'osCommerce/OM/') {
                    $writable = self::is_writable(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $d) && self::is_writable(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . dirname($d));
                } elseif (substr($d, 0, 7) == 'public/') {
                    $writable = self::is_writable(realpath(OSCOM::get_config('dir_fs_public', 'OSCOM') . '../') . '/' . $d) && self::is_writable(realpath(OSCOM::get_config('dir_fs_public', 'OSCOM') . '../') . '/' . dirname($d));
                }
                $result['entries'][] = ['key' => $counter, 'name' => $d, 'exists' => true, 'writable' => $writable, 'custom' => $custom, 'to_delete' => true];
                $counter++;
            }
        }
        $result['total'] = count($result['entries']);
        return $result;
    }
    public static function is_writable($location)
    {
        if (!file_exists($location)) {
            while (true) {
                $location = dirname($location);
                if (file_exists($location)) {
                    break;
                }
            }
        }
        return is_writable($location);
    }
}