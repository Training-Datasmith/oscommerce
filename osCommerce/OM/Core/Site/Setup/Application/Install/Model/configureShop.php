<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Setup\Application\Install\Model;

use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\PDO;
use Os_Commerce\OM\Core\Registry;
use Os_Commerce\OM\Core\Site\Admin\Application\Administrators\Administrators;
class Configure_Shop
{
    public static function execute($data)
    {
        Registry::set('PDO', PDO::initialize($data['server'], $data['username'], $data['password'], $data['database'], $data['port'], $data['class']));
        OSCOM::set_config('db_table_prefix', $data['table_prefix'], 'Admin');
        OSCOM::set_config('db_table_prefix', $data['table_prefix'], 'Shop');
        OSCOM::set_config('db_table_prefix', $data['table_prefix'], 'Setup');
        $cfg_data = [['key' => 'STORE_NAME', 'value' => $data['shop_name']], ['key' => 'STORE_OWNER', 'value' => $data['shop_owner_name']], ['key' => 'STORE_OWNER_EMAIL_ADDRESS', 'value' => $data['shop_owner_email']], ['key' => 'EMAIL_FROM', 'value' => '"' . $data['shop_owner_name'] . '" <' . $data['shop_owner_email'] . '>']];
        OSCOM::call_db('Admin\UpdateConfigurationParameters', $cfg_data, 'Site');
        $admin_data = ['username' => $data['admin_username'], 'password' => $data['admin_password'], 'modules' => ['0']];
        Administrators::save($admin_data);
    }
}