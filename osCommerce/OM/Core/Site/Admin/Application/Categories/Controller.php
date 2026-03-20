<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Categories;

use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Category_Tree;
/**
 * @since v3.0.2
 */
class Controller extends \Os_Commerce\OM\Core\Site\Admin\Application_Abstract
{
    protected $_group = 'products';
    protected $_icon = 'categories.png';
    protected $_sort_order = 200;
    protected $_category_id = 0;
    protected $_tree = [];
    protected function initialize()
    {
        $this->_title = OSCOM::get_def('app_title');
    }
    protected function process()
    {
        $oscom_message_stack = Registry::get('MessageStack');
        $this->_page_title = OSCOM::get_def('heading_title');
        if (isset($_GET['cid']) && is_numeric($_GET['cid'])) {
            $this->_category_id = $_GET['cid'];
        }
        $this->_tree = new Category_Tree();
        Registry::set('CategoryTree', $this->_tree);
        // check if the categories image directory exists
        if (is_dir(OSCOM::get_config('dir_fs_public', 'OSCOM') . 'categories')) {
            if (!is_writeable(OSCOM::get_config('dir_fs_public', 'OSCOM') . 'categories')) {
                $oscom_message_stack->add('header', sprintf(OSCOM::get_def('ms_error_image_directory_not_writable'), OSCOM::get_config('dir_fs_public', 'OSCOM') . 'categories'), 'error');
            }
        } else {
            $oscom_message_stack->add('header', sprintf(OSCOM::get_def('ms_error_image_directory_non_existant'), OSCOM::get_config('dir_fs_public', 'OSCOM') . 'categories'), 'error');
        }
    }
    public function get_current_category_id()
    {
        return $this->_category_id;
    }
    public function get_tree()
    {
        return $this->_tree;
    }
    public function get_category_list()
    {
        $CT = $this->_tree;
        $CT->reset();
        $CT->set_spacer_string('&nbsp;');
        $categories_array = [];
        foreach ($CT->get_array() as $value) {
            $cpath = explode('_', $value['id']);
            // end() only accepts variables
            $categories_array[] = ['id' => end($cpath), 'text' => $value['title']];
        }
        return $categories_array;
    }
}