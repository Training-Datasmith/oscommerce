<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\Registry;

/**
 * Insert minimal customer + order rows when the harness DB has products but no customers.
 */
final class ShopHarnessDataSeeder
{
    /** @var array{customer_id: int, address_id: int, order_id: int}|null */
    private static ?array $ids = null;

    /**
     * @return array{customer_id: int, address_id: int, order_id: int}
     */
    public static function ensureBaselineData(): array
    {
        if (self::$ids !== null) {
            return self::$ids;
        }

        $pdo = Registry::get('PDO');

        $customerId = (int) ($pdo->query('select customers_id from osc_customers order by customers_id limit 1')->fetchColumn() ?: 0);
        $addressId = 0;
        $orderId = (int) ($pdo->query('select orders_id from osc_orders order by orders_id limit 1')->fetchColumn() ?: 0);

        if ($customerId < 1) {
            $now = date('Y-m-d H:i:s');
            $pdo->prepare(
                'insert into osc_customers (customers_gender, customers_firstname, customers_lastname, customers_email_address, customers_telephone, customers_password, customers_newsletter, customers_status, date_account_created, date_account_last_modified) values (:gender, :firstname, :lastname, :email, :telephone, :password, :newsletter, 1, :created, :modified)'
            )->execute([
                ':gender' => 'm',
                ':firstname' => 'Coverage',
                ':lastname' => 'Customer',
                ':email' => 'coverage-customer@example.test',
                ':telephone' => '555-0199',
                ':password' => 'test',
                ':newsletter' => '0',
                ':created' => $now,
                ':modified' => $now,
            ]);
            $customerId = (int) $pdo->lastInsertId();

            $pdo->prepare(
                'insert into osc_address_book (customers_id, entry_gender, entry_firstname, entry_lastname, entry_street_address, entry_city, entry_postcode, entry_state, entry_country_id, entry_zone_id, entry_telephone) values (:cid, :gender, :firstname, :lastname, :street, :city, :postcode, :state, :country_id, :zone_id, :telephone)'
            )->execute([
                ':cid' => $customerId,
                ':gender' => 'm',
                ':firstname' => 'Coverage',
                ':lastname' => 'Customer',
                ':street' => '123 Main St',
                ':city' => 'Testville',
                ':postcode' => '90210',
                ':state' => 'CA',
                ':country_id' => 223,
                ':zone_id' => 1,
                ':telephone' => '555-0199',
            ]);
            $addressId = (int) $pdo->lastInsertId();

            $pdo->prepare('update osc_customers set customers_default_address_id = :aid where customers_id = :cid')
                ->execute([':aid' => $addressId, ':cid' => $customerId]);
        } else {
            $addressId = (int) ($pdo->query('select customers_default_address_id from osc_customers where customers_id = ' . $customerId)->fetchColumn() ?: 0);
        }

        if ($orderId < 1 && $customerId > 0) {
            $productId = (int) ($pdo->query('select products_id from osc_products order by products_id limit 1')->fetchColumn() ?: 0);
            if ($productId > 0) {
                $now = date('Y-m-d H:i:s');
                $pdo->prepare(
                    'insert into osc_orders (customers_id, customers_name, customers_street_address, customers_city, customers_country, customers_country_iso2, customers_country_iso3, customers_address_format, customers_email_address, delivery_name, delivery_street_address, delivery_city, delivery_country, delivery_country_iso2, delivery_country_iso3, delivery_address_format, billing_name, billing_street_address, billing_city, billing_country, billing_country_iso2, billing_country_iso3, billing_address_format, payment_method, payment_module, date_purchased, orders_status, currency, currency_value) values (:customers_id, :customers_name, :street, :city, :country, :iso2, :iso3, :format, :email, :delivery_name, :dstreet, :dcity, :dcountry, :diso2, :diso3, :dformat, :billing_name, :bstreet, :bcity, :bcountry, :biso2, :biso3, :bformat, :payment_method, :payment_module, :purchased, 1, :currency, 1.000000)'
                )->execute([
                    ':customers_id' => $customerId,
                    ':customers_name' => 'Coverage Customer',
                    ':street' => '123 Main St',
                    ':city' => 'Testville',
                    ':country' => 'United States',
                    ':iso2' => 'US',
                    ':iso3' => 'USA',
                    ':format' => '2',
                    ':email' => 'coverage-customer@example.test',
                    ':delivery_name' => 'Coverage Customer',
                    ':dstreet' => '123 Main St',
                    ':dcity' => 'Testville',
                    ':dcountry' => 'United States',
                    ':diso2' => 'US',
                    ':diso3' => 'USA',
                    ':dformat' => '2',
                    ':billing_name' => 'Coverage Customer',
                    ':bstreet' => '123 Main St',
                    ':bcity' => 'Testville',
                    ':bcountry' => 'United States',
                    ':biso2' => 'US',
                    ':biso3' => 'USA',
                    ':bformat' => '2',
                    ':payment_method' => 'Cash On Delivery',
                    ':payment_module' => 'cod',
                    ':purchased' => $now,
                    ':currency' => 'USD',
                ]);
                $orderId = (int) $pdo->lastInsertId();

                $pdo->prepare(
                    'insert into osc_orders_products (orders_id, products_id, products_model, products_name, products_price, products_tax, products_quantity) values (:oid, :pid, :model, :name, 10.0000, 0.0000, 1)'
                )->execute([
                    ':oid' => $orderId,
                    ':pid' => $productId,
                    ':model' => 'cov',
                    ':name' => 'Coverage Product',
                ]);

                $pdo->prepare(
                    'insert into osc_orders_total (orders_id, title, text, value, class, sort_order) values (:oid, :title, :text, :value, :class, :sort)'
                )->execute([
                    ':oid' => $orderId,
                    ':title' => 'Flat Rate:',
                    ':text' => '$5.00',
                    ':value' => '5.0000',
                    ':class' => 'Shipping',
                    ':sort' => 30,
                ]);
                $pdo->prepare(
                    'insert into osc_orders_total (orders_id, title, text, value, class, sort_order) values (:oid, :title, :text, :value, :class, :sort)'
                )->execute([
                    ':oid' => $orderId,
                    ':title' => 'Total:',
                    ':text' => '$10.00',
                    ':value' => '10.0000',
                    ':class' => 'Total',
                    ':sort' => 999,
                ]);
            }
        }

        self::$ids = [
            'customer_id' => $customerId,
            'address_id' => $addressId,
            'order_id' => $orderId,
        ];

        return self::$ids;
    }
}
