<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Shop\Application\Cart\Action;

use osCommerce\OM\Core\ApplicationAbstract;
use osCommerce\OM\Core\OSCOM;
use osCommerce\OM\Core\Registry;

class Delete
{
    public static function execute(ApplicationAbstract $application)
    {
        $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

        if (is_numeric($_GET['Delete'])) {
            $OSCOM_ShoppingCart->remove($_GET['Delete']);
        }

        OSCOM::redirect(OSCOM::getLink(null, 'Cart'));
    }
}
