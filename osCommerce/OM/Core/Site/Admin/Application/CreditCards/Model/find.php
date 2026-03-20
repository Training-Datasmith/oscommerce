<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Credit_Cards\Model;

use Os_Commerce\OM\Core\OSCOM;
class find
{
    public static function execute($search, $pageset = 1)
    {
        $data = ['keywords' => $search, 'batch_pageset' => $pageset, 'batch_max_results' => MAX_DISPLAY_SEARCH_RESULTS];
        if (!is_numeric($data['batch_pageset']) || floor($data['batch_pageset']) != $data['batch_pageset']) {
            $data['batch_pageset'] = 1;
        }
        return OSCOM::call_db('Admin\CreditCards\Find', $data);
    }
}