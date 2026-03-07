<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Admin\Application\Categories\RPC;

use osCommerce\OM\Core\Site\Admin\Application\Categories\Categories;
use osCommerce\OM\Core\Site\RPC\Controller as RPC;

/**
 * @since v3.0.2
 */

class SaveSortOrder
{
    public static function execute()
    {
        $result = [];

        $data = [];
        $counter = 0;

        foreach ($_GET['row'] as $row) {
            $data[] = ['id' => $row,
                            'sort_order' => $counter];

            $counter++;
        }

        if (Categories::saveSortOrder($data)) {
            $result['rpcStatus'] = RPC::STATUS_SUCCESS;
        }

        echo json_encode($result);
    }
}
