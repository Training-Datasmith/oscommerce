<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin;

use Os_Commerce\OM\Core\Registry;
/**
 * @since v3.0.2
 */
class Category_Tree extends \Os_Commerce\OM\Core\Site\Shop\Category_Tree
{
    protected $_show_total_products = true;
    public function __construct()
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Language = Registry::get('Language');
        $Qcategories = $OSCOM_PDO->prepare('select c.categories_id, c.parent_id, c.categories_image, cd.categories_name from :table_categories c, :table_categories_description cd where c.categories_id = cd.categories_id and cd.language_id = :language_id order by c.parent_id, c.sort_order, cd.categories_name');
        $Qcategories->bind_int(':language_id', $OSCOM_Language->get_id());
        $Qcategories->execute();
        $this->_data = [];
        while ($Qcategories->fetch()) {
            $this->_data[$Qcategories->value_int('parent_id')][$Qcategories->value_int('categories_id')] = ['name' => $Qcategories->value('categories_name'), 'image' => $Qcategories->value('categories_image'), 'count' => 0];
        }
        $this->_calculate_product_totals(false);
    }
    public function get_path($category_id, $level = 0, $separator = ' ')
    {
        $path = '';
        foreach ($this->_data as $parent => $categories) {
            foreach ($categories as $id => $info) {
                if ($id == $category_id) {
                    if ($level < 1) {
                        $path = $info['name'];
                    } else {
                        $path = $info['name'] . $separator . $path;
                    }
                    if ($parent != $this->root_category_id) {
                        $path = $this->get_path($parent, $level + 1, $separator) . $path;
                    }
                }
            }
        }
        return $path;
    }
    public function get_path_array($category_id)
    {
        static $path = [];
        foreach ($this->_data as $parent => $categories) {
            foreach ($categories as $id => $info) {
                if ($id == $category_id) {
                    $path[] = ['id' => $id, 'name' => $info['name']];
                    if ($parent != $this->root_category_id) {
                        $this->get_path_array($parent);
                    }
                }
            }
        }
        return array_reverse($path);
    }
}