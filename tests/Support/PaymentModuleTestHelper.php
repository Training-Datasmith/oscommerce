<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\Registry;

/**
 * Define payment module MODULE_* constants when missing from harness configuration.
 */
final class PaymentModuleTestHelper
{
    public static function definePayPalExpressCheckout(): void
    {
        $defaults = [
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_STATUS' => '1',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SORT_ORDER' => '0',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ORDER_STATUS_ID' => '0',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ZONE' => '0',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_SERVER' => 'Sandbox',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_METHOD' => 'Sale',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ACCOUNT_OPTIONAL' => '0',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_INSTANT_UPDATE' => '0',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_USERNAME' => '',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_PASSWORD' => '',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_API_SIGNATURE' => '',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SELLER_ACCOUNT' => 'seller@example.test',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_DEBUG_EMAIL' => '',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_IMAGE' => 'Static',
        ];

        $pdo = Registry::get('PDO');
        $stmt = $pdo->query("select configuration_key, configuration_value from osc_configuration where configuration_key like 'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_%'");
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if (!\defined($row['configuration_key'])) {
                \define($row['configuration_key'], $row['configuration_value']);
            }
        }

        foreach ($defaults as $key => $value) {
            if (!\defined($key)) {
                \define($key, $value);
            }
        }
    }
}
