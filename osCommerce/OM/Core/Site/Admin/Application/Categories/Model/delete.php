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
class delete
{
    public static function execute($id)
    {
        if (Registry::exists('CategoryTree')) {
            $oscom_category_tree = Registry::get('CategoryTree');
        } else {
            $oscom_category_tree = new Category_Tree();
            Registry::set('CategoryTree', $oscom_category_tree);
        }
        $data = ['id' => $id];
        foreach (array_merge([$data['id']], $oscom_category_tree->get_children($data['id'])) as $c) {
            Categories::delete_image($c);
        }
        if (OSCOM::call_db('Admin\Categories\Delete', $data)) {
            Cache::clear('categories');
            Cache::clear('category_tree');
            Cache::clear('also_purchased');
            return true;
        }
        return false;
    }
}