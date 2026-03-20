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
class Delete_Image
{
    public static function execute($id)
    {
        if (Registry::exists('CategoryTree')) {
            $oscom_category_tree = Registry::get('CategoryTree');
        } else {
            $oscom_category_tree = new Category_Tree();
            Registry::set('CategoryTree', $oscom_category_tree);
        }
        $data = $oscom_category_tree->get_data($id);
        if (!empty($data['image']) && file_exists(OSCOM::get_config('dir_fs_public', 'OSCOM') . 'categories/' . $data['image'])) {
            unlink(OSCOM::get_config('dir_fs_public', 'OSCOM') . 'categories/' . $data['image']);
        }
    }
}