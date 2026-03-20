<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Module\Service;

use Os_Commerce\OM\Core\OSCOM;
/**
 * @since v3.0.2
 */
class Specials extends \Os_Commerce\OM\Core\Site\Admin\Service_Abstract
{
    protected function initialize()
    {
        $this->title = OSCOM::get_def('services_specials_title');
        $this->description = OSCOM::get_def('services_specials_description');
    }
    public function install()
    {
        $data = ['title' => 'Special Products', 'key' => 'MAX_DISPLAY_SPECIAL_PRODUCTS', 'value' => '9', 'description' => 'Maximum number of products on special to display', 'group_id' => '6'];
        OSCOM::call_db('Admin\InsertConfigurationParameters', $data, 'Site');
    }
    public function remove()
    {
        OSCOM::call_db('Admin\DeleteConfigurationParameters', $this->keys(), 'Site');
    }
    public function keys()
    {
        return ['MAX_DISPLAY_SPECIAL_PRODUCTS'];
    }
}