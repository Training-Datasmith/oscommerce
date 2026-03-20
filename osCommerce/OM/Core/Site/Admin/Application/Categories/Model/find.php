<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Categories\Model;

use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Category_Tree;
/**
 * @since v3.0.2
 */
class find
{
    public static function execute($search, $parent_id = 0)
    {
        if (Registry::exists('CategoryTree')) {
            $oscom_category_tree = Registry::get('CategoryTree');
        } else {
            $oscom_category_tree = new Category_Tree();
            Registry::set('CategoryTree', $oscom_category_tree);
        }
        $oscom_category_tree->reset();
        $oscom_category_tree->set_root_category_id($parent_id);
        $oscom_category_tree->set_breadcrumb_usage(false);
        $categories = [];
        foreach ($oscom_category_tree->get_array() as $c) {
            if (stripos($c['title'], $search) !== false) {
                if ($c['id'] != $parent_id) {
                    $category_path = $oscom_category_tree->get_path_array($c['id']);
                    $top_category_id = $category_path[0]['id'];
                    if (!in_array($top_category_id, $categories)) {
                        $categories[] = $top_category_id;
                    }
                }
            }
        }
        $result = ['entries' => []];
        foreach ($categories as $c) {
            $result['entries'][] = ['id' => $oscom_category_tree->get_data($c, 'id'), 'title' => $oscom_category_tree->get_data($c, 'name'), 'products' => $oscom_category_tree->get_data($c, 'count')];
        }
        $result['total'] = count($result['entries']);
        return $result;
    }
}