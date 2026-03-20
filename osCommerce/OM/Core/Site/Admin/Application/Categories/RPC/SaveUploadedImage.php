<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Categories\RPC;

use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Upload;
/**
 * @since v3.0.2
 */
class Save_Uploaded_Image
{
    public static function execute()
    {
        $error = true;
        $image = new Upload('qqfile', OSCOM::get_config('dir_fs_public', 'OSCOM') . 'upload', null, ['gif', 'jpg', 'png']);
        if ($image->check() && $image->save()) {
            $error = false;
        }
        if ($error === false) {
            $result = ['success' => true, 'filename' => $image->get_filename()];
        } else {
            $result = ['error' => 'Error'];
        }
        echo json_encode($result);
    }
}