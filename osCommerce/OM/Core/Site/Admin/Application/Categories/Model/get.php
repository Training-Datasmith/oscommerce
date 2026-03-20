<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Categories\Model;

use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Category_Tree;
/**
 * @since v3.0.2
 */
class get
{
    public static function execute($id, $key = null, $language_id = null)
    {
        $OSCOM_Language = Registry::get('Language');
        if (Registry::exists('CategoryTree')) {
            $oscom_category_tree = Registry::get('CategoryTree');
        } else {
            $oscom_category_tree = new Category_Tree();
            Registry::set('CategoryTree', $oscom_category_tree);
        }
        if (!isset($language_id)) {
            $language_id = $OSCOM_Language->get_id();
        }
        $data = ['id' => $id, 'language_id' => $language_id];
        $result = OSCOM::call_db('Admin\Categories\Get', $data);
        $result['children_count'] = count($oscom_category_tree->get_children($id));
        $result['product_count'] = $oscom_category_tree->get_number_of_products($id);
        if (isset($key)) {
            $result = $result[$key] ?: null;
        }
        return $result;
    }
}