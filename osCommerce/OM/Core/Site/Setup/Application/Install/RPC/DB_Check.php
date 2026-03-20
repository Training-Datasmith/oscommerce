<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Setup\Application\Install\RPC;

use Os_Commerce\OM\Core\Site\Setup\Application\Install\Install;
class Db_Check
{
    public static function execute()
    {
        $data = ['server' => trim(urldecode($_POST['server'])), 'username' => trim(urldecode($_POST['username'])), 'password' => trim(urldecode($_POST['password'])), 'database' => trim(urldecode($_POST['name'])), 'port' => trim(urldecode($_POST['port'])), 'class' => str_replace('_', '\\', trim(urldecode($_POST['class'])))];
        try {
            if (empty($data['database'])) {
                throw new \Exception('Database does not exist.');
            }
            Install::check_db($data);
            $result = ['result' => true];
        } catch (\Exception $e) {
            $result = ['result' => false, 'error_message' => $e->get_message()];
        }
        echo json_encode($result);
    }
}