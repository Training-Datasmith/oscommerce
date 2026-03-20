<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Core_Update\Model;

use Os_Commerce\OM\Core\Http_Request;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Core_Update\Core_Update;
class Download_Package
{
    public static function execute($version = null)
    {
        if (empty($version)) {
            $link = Core_Update::get_available_package_info('update_package');
        } else {
            $versions = Core_Update::get_available_packages();
            foreach ($versions['entries'] as $v) {
                if ($v['version'] == $version) {
                    $link = $v['update_package'];
                    break;
                }
            }
        }
        $response = Http_Request::get_response(['url' => $link, 'parameters' => 'check=true']);
        return file_put_contents(OSCOM::BASE_DIRECTORY . 'Work/CoreUpdate/update.phar', $response);
    }
}