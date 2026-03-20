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
class Get_All
{
    public static function execute($parent_id = 0)
    {
        if (Registry::exists('CategoryTree')) {
            $oscom_category_tree = Registry::get('CategoryTree');
        } else {
            $oscom_category_tree = new Category_Tree();
            Registry::set('CategoryTree', $oscom_category_tree);
        }
        $oscom_category_tree->reset();
        $oscom_category_tree->set_maximum_level(1);
        $oscom_category_tree->set_breadcrumb_usage(false);
        $result = $oscom_category_tree->get_array($parent_id);
        foreach ($result as &$c) {
            $c['products'] = $oscom_category_tree->get_data($c['id'], 'count');
        }
        return ['entries' => $result, 'total' => count($result)];
    }
}