<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\Registry;

/**
 * Define payment module MODULE_* constants when missing from harness configuration.
 */
final class PaymentModuleTestHelper
{
    /**
     * Update harness DB PayPal module keys before Shop boot (constants load from configuration).
     *
     * @param array<string, string> $values configuration_key => configuration_value
     */
    public static function applyPayPalDbOverrides(array $values): void
    {
        if (Registry::exists('PDO')) {
            self::applyPayPalDbOverridesOnPdo(Registry::get('PDO'), $values);
            return;
        }

        self::applyPayPalDbOverridesOnPdo(self::harnessPdo(), $values);
    }

    /**
     * @param array<string, string> $values
     */
    private static function applyPayPalDbOverridesOnPdo(\PDO $pdo, array $values): void
    {
        $update = $pdo->prepare(
            'update osc_configuration set configuration_value = :value where configuration_key = :key'
        );
        $insert = $pdo->prepare(
            'insert into osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added)
             values (:title, :key, :value, :description, :group_id, 0, now())'
        );

        foreach ($values as $key => $value) {
            if (!str_starts_with($key, 'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_')) {
                continue;
            }
            $update->execute([':value' => $value, ':key' => $key]);
            if ($update->rowCount() < 1) {
                $insert->execute([
                    ':title' => $key,
                    ':key' => $key,
                    ':value' => $value,
                    ':description' => 'PayPal Express Checkout (coverage harness)',
                    ':group_id' => 6,
                ]);
            }
        }
    }

    private static function harnessPdo(): \PDO
    {
        $host = getenv('OSCOMMERCE_DB_SERVER') ?: getenv('OSCOMMERCE_DB_HOST') ?: '127.0.0.1';
        $name = getenv('OSCOMMERCE_DB_NAME') ?: 'oscommerce_test';
        $user = getenv('OSCOMMERCE_DB_USER') ?: 'oscommerce';
        $pass = getenv('OSCOMMERCE_DB_PASSWORD') ?: getenv('OSCOMMERCE_DB_PASS') ?: 'oscommerce';

        return new \PDO(
            'mysql:host=' . $host . ';dbname=' . $name . ';charset=utf8mb4',
            $user,
            $pass
        );
    }

    public static function definePayPalExpressCheckout(bool $instantUpdate = false): void
    {
        $defaults = [
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_STATUS' => '1',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_SORT_ORDER' => '0',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ORDER_STATUS_ID' => '0',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ZONE' => '0',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_SERVER' => 'Sandbox',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_TRANSACTION_METHOD' => 'Sale',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_ACCOUNT_OPTIONAL' => '0',
            'MODULE_PAYMENT_PAYPAL_EXPRESS_CHECKOUT_INSTANT_UPDATE' => $instantUpdate ? '1' : '0',
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
