<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Categories\Model;

use Os_Commerce\OM\Core\Cache;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Categories\Categories;
use Os_Commerce\OM\Core\Site\Admin\Category_Tree;
/**
 * @since v3.0.2
 */
class save
{
    public static function execute($id = null, $data)
    {
        if (Registry::exists('CategoryTree')) {
            $oscom_category_tree = Registry::get('CategoryTree');
        } else {
            $oscom_category_tree = new Category_Tree();
            Registry::set('CategoryTree', $oscom_category_tree);
        }
        if (is_numeric($id)) {
            $data['id'] = $id;
        }
        // Prevent another big bang and check if category is not being moved to a child category
        if (isset($data['id']) && $oscom_category_tree->get_parent_id($data['id']) != $data['parent_id']) {
            if (in_array($data['id'], explode('_', $oscom_category_tree->build_breadcrumb($data['parent_id'])))) {
                return false;
            }
        }
        if (isset($data['image'])) {
            $new_image = $data['image'];
            while (file_exists(OSCOM::get_config('dir_fs_public', 'OSCOM') . 'categories/' . $new_image)) {
                $new_image = rand(10, 99) . $new_image;
            }
            if (rename(OSCOM::get_config('dir_fs_public', 'OSCOM') . 'upload/' . $data['image'], OSCOM::get_config('dir_fs_public', 'OSCOM') . 'categories/' . $new_image)) {
                if (is_numeric($id)) {
                    $old_image = Categories::get($id, 'categories_image');
                    unlink(OSCOM::get_config('dir_fs_public', 'OSCOM') . 'categories/' . $old_image);
                }
                $data['image'] = $new_image;
            } else {
                $data['image'] = null;
            }
        }
        if (OSCOM::call_db('Admin\Categories\Save', $data)) {
            Cache::clear('categories');
            Cache::clear('category_tree');
            Cache::clear('also_purchased');
            return true;
        }
        return false;
    }
}