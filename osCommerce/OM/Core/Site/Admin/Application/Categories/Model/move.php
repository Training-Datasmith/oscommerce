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
use Os_Commerce\OM\Core\Site\Admin\Category_Tree;
/**
 * @since v3.0.2
 */
class move
{
    public static function execute($id, $parent_id)
    {
        if (Registry::exists('CategoryTree')) {
            $oscom_category_tree = Registry::get('CategoryTree');
        } else {
            $oscom_category_tree = new Category_Tree();
            Registry::set('CategoryTree', $oscom_category_tree);
        }
        $data = ['id' => $id, 'parent_id' => $parent_id];
        // Prevent another big bang and check if category is not being moved to a child category
        if ($oscom_category_tree->get_parent_id($data['id']) != $data['parent_id']) {
            if (in_array($data['id'], explode('_', $oscom_category_tree->build_breadcrumb($data['parent_id'])))) {
                return false;
            }
        }
        if (OSCOM::call_db('Admin\Categories\Move', $data)) {
            Cache::clear('categories');
            Cache::clear('category_tree');
            Cache::clear('also_purchased');
            return true;
        }
        return false;
    }
}