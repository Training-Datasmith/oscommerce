<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core;

class Directory_Listing
{
    protected $_directory = '';
    protected $_include_files = true;
    protected $_include_directories = true;
    protected $_exclude_entries = ['.', '..'];
    protected $_stats = false;
    protected $_recursive = false;
    protected $_check_extension = [];
    protected $_add_directory_to_filename = false;
    protected $_listing;
    public function __construct($directory = '', $stats = false)
    {
        $this->set_directory(realpath($directory));
        $this->set_stats($stats);
    }
    public function set_directory($directory)
    {
        $this->_directory = $directory;
    }
    public function set_include_files($boolean)
    {
        if ($boolean === true) {
            $this->_include_files = true;
        } else {
            $this->_include_files = false;
        }
    }
    public function set_include_directories($boolean)
    {
        if ($boolean === true) {
            $this->_include_directories = true;
        } else {
            $this->_include_directories = false;
        }
    }
    public function set_exclude_entries($entries)
    {
        if (is_array($entries)) {
            foreach ($entries as $value) {
                if (!in_array($value, $this->_exclude_entries)) {
                    $this->_exclude_entries[] = $value;
                }
            }
        } elseif (is_string($entries)) {
            if (!in_array($entries, $this->_exclude_entries)) {
                $this->_exclude_entries[] = $entries;
            }
        }
    }
    public function set_stats($boolean)
    {
        if ($boolean === true) {
            $this->_stats = true;
        } else {
            $this->_stats = false;
        }
    }
    public function set_recursive($boolean)
    {
        if ($boolean === true) {
            $this->_recursive = true;
        } else {
            $this->_recursive = false;
        }
    }
    public function set_check_extension($extension)
    {
        $this->_check_extension[] = strtolower($extension);
    }
    public function set_add_directory_to_filename($boolean)
    {
        if ($boolean === true) {
            $this->_add_directory_to_filename = true;
        } else {
            $this->_add_directory_to_filename = false;
        }
    }
    public function read($directory = '')
    {
        if (empty($directory)) {
            $directory = $this->_directory;
        }
        if (!is_array($this->_listing)) {
            $this->_listing = [];
        }
        if ($dir = @dir($directory)) {
            while (($entry = $dir->read()) !== false) {
                if (!in_array($entry, $this->_exclude_entries)) {
                    if ($this->_include_files === true && is_file($dir->path . '/' . $entry)) {
                        if (empty($this->_check_extension) || in_array(strtolower(substr($entry, strrpos($entry, '.') + 1)), $this->_check_extension)) {
                            if ($this->_add_directory_to_filename === true) {
                                if ($dir->path != $this->_directory) {
                                    $entry = substr($dir->path, strlen($this->_directory) + 1) . '/' . $entry;
                                }
                            }
                            $this->_listing[] = ['name' => $entry, 'is_directory' => false];
                            if ($this->_stats === true) {
                                $stats = ['size' => filesize($dir->path . '/' . $entry), 'permissions' => fileperms($dir->path . '/' . $entry), 'user_id' => fileowner($dir->path . '/' . $entry), 'group_id' => filegroup($dir->path . '/' . $entry), 'last_modified' => filemtime($dir->path . '/' . $entry)];
                                $this->_listing[sizeof($this->_listing) - 1] = array_merge($this->_listing[sizeof($this->_listing) - 1], $stats);
                            }
                        }
                    } elseif (is_dir($dir->path . '/' . $entry)) {
                        if ($this->_include_directories === true) {
                            $entry_name = $entry;
                            if ($this->_add_directory_to_filename === true) {
                                if ($dir->path != $this->_directory) {
                                    $entry_name = substr($dir->path, strlen($this->_directory) + 1) . '/' . $entry;
                                }
                            }
                            $this->_listing[] = ['name' => $entry_name, 'is_directory' => true];
                            if ($this->_stats === true) {
                                $stats = ['size' => filesize($dir->path . '/' . $entry), 'permissions' => fileperms($dir->path . '/' . $entry), 'user_id' => fileowner($dir->path . '/' . $entry), 'group_id' => filegroup($dir->path . '/' . $entry), 'last_modified' => filemtime($dir->path . '/' . $entry)];
                                $this->_listing[sizeof($this->_listing) - 1] = array_merge($this->_listing[sizeof($this->_listing) - 1], $stats);
                            }
                        }
                        if ($this->_recursive === true) {
                            $this->read($dir->path . '/' . $entry);
                        }
                    }
                }
            }
            $dir->close();
            unset($dir);
        }
    }
    public function get_files($sort_by_directories = true)
    {
        if (!is_array($this->_listing)) {
            $this->read();
        }
        if (is_array($this->_listing) && sizeof($this->_listing) > 0) {
            if ($sort_by_directories === true) {
                usort($this->_listing, [$this, '_sortListing']);
            }
            return $this->_listing;
        }
        return [];
    }
    public function get_size()
    {
        if (!is_array($this->_listing)) {
            $this->read();
        }
        return sizeof($this->_listing);
    }
    public function get_directory()
    {
        return $this->_directory;
    }
    protected function _sort_listing($a, $b)
    {
        return strcmp(($a['is_directory'] === true ? 'D' : 'F') . $a['name'], ($b['is_directory'] === true ? 'D' : 'F') . $b['name']);
    }
}