<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Shop\Application\Account\Action\Notifications;

use Os_Commerce\OM\Core\Application_Abstract;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
class Process
{
    public static function execute(Application_Abstract $application)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Customer = Registry::get('Customer');
        $oscom_message_stack = Registry::get('MessageStack');
        $updated = false;
        if (isset($_POST['product_global']) && is_numeric($_POST['product_global'])) {
            $product_global = (int) $_POST['product_global'];
        } else {
            $product_global = 0;
        }
        if (isset($_POST['products'])) {
            (array) $products = $_POST['products'];
        } else {
            $products = [];
        }
        // HPDL Should be moved to the customers class!
        $Qglobal = $OSCOM_PDO->prepare('select global_product_notifications from :table_customers where customers_id = :customers_id');
        $Qglobal->bind_int(':customers_id', $OSCOM_Customer->get_id());
        $Qglobal->execute();
        if ($product_global !== $Qglobal->value_int('global_product_notifications')) {
            $product_global = $Qglobal->value_int('global_product_notifications') === 1 ? 0 : 1;
            $Qupdate = $OSCOM_PDO->prepare('update :table_customers set global_product_notifications = :global_product_notifications where customers_id = :customers_id');
            $Qupdate->bind_int(':global_product_notifications', $product_global);
            $Qupdate->bind_int(':customers_id', $OSCOM_Customer->get_id());
            $Qupdate->execute();
            if ($Qupdate->row_count() === 1) {
                $updated = true;
            }
        } elseif (count($products) > 0) {
            $products_parsed = array_filter($products, 'is_numeric');
            if (count($products_parsed) > 0) {
                $Qcheck = $OSCOM_PDO->prepare('select count(*) as total from :table_products_notifications where customers_id = :customers_id and products_id not in (' . implode(',', $products_parsed) . ')');
                $Qcheck->bind_int(':customers_id', $OSCOM_Customer->get_id());
                $Qcheck->execute();
                if ($Qcheck->value_int('total') > 0) {
                    $Qdelete = $OSCOM_PDO->prepare('delete from :table_products_notifications where customers_id = :customers_id and products_id not in (' . implode(',', $products_parsed) . ')');
                    $Qdelete->bind_int(':customers_id', $OSCOM_Customer->get_id());
                    $Qdelete->execute();
                    if ($Qdelete->row_count() > 0) {
                        $updated = true;
                    }
                }
            }
        } else {
            $Qcheck = $OSCOM_PDO->prepare('select count(*) as total from :table_products_notifications where customers_id = :customers_id');
            $Qcheck->bind_int(':customers_id', $OSCOM_Customer->get_id());
            $Qcheck->execute();
            if ($Qcheck->value_int('total') > 0) {
                $Qdelete = $OSCOM_PDO->prepare('delete from :table_products_notifications where customers_id = :customers_id');
                $Qdelete->bind_int(':customers_id', $OSCOM_Customer->get_id());
                $Qdelete->execute();
                if ($Qdelete->row_count() > 0) {
                    $updated = true;
                }
            }
        }
        if ($updated === true) {
            $oscom_message_stack->add('Account', OSCOM::get_def('success_notifications_updated'), 'success');
        }
        OSCOM::redirect(OSCOM::get_link(null, null, null, 'SSL'));
    }
}