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
class Currencies extends \Os_Commerce\OM\Core\Site\Admin\Service_Abstract
{
    public $uninstallable = false;
    public $depends = 'Language';
    protected function initialize()
    {
        $this->title = OSCOM::get_def('services_currencies_title');
        $this->description = OSCOM::get_def('services_currencies_description');
    }
    public function install()
    {
        $data = ['title' => 'Use Default Language Currency', 'key' => 'USE_DEFAULT_LANGUAGE_CURRENCY', 'value' => '-1', 'description' => 'Automatically use the currency set with the language (eg, German->Euro).', 'group_id' => '6', 'use_function' => 'osc_cfg_use_get_boolean_value', 'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))'];
        OSCOM::call_db('Admin\InsertConfigurationParameters', $data, 'Site');
    }
    public function remove()
    {
        OSCOM::call_db('Admin\DeleteConfigurationParameters', $this->keys(), 'Site');
    }
    public function keys()
    {
        return ['USE_DEFAULT_LANGUAGE_CURRENCY'];
    }
}