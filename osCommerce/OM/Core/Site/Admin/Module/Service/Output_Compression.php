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
class Output_Compression extends \Os_Commerce\OM\Core\Site\Admin\Service_Abstract
{
    public $precedes = 'Session';
    protected function initialize()
    {
        $this->title = OSCOM::get_def('services_output_compression_title');
        $this->description = OSCOM::get_def('services_output_compression_description');
    }
    public function install()
    {
        $data = ['title' => 'GZIP Compression Level', 'key' => 'SERVICE_OUTPUT_COMPRESSION_GZIP_LEVEL', 'value' => '5', 'description' => 'Set the GZIP compression level to this value (0=min, 9=max).', 'group_id' => '6', 'set_function' => 'osc_cfg_set_boolean_value(array(\'0\', \'1\', \'2\', \'3\', \'4\', \'5\', \'6\', \'7\', \'8\', \'9\'))'];
        OSCOM::call_db('Admin\InsertConfigurationParameters', $data, 'Site');
    }
    public function remove()
    {
        OSCOM::call_db('Admin\DeleteConfigurationParameters', $this->keys(), 'Site');
    }
    public function keys()
    {
        return ['SERVICE_OUTPUT_COMPRESSION_GZIP_LEVEL'];
    }
}