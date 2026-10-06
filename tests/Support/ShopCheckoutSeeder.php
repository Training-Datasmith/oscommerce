<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Payment;
use osCommerce\OM\Core\Site\Shop\ShoppingCart;

/**
 * Seed a guest cart/customer state suitable for Checkout routes (no customers in sample DB).
 */
final class ShopCheckoutSeeder
{
    /**
     * @return array<string, mixed>
     */
    public static function sampleAddress(): array
    {
        return [
            'gender' => 'm',
            'firstname' => 'Test',
            'lastname' => 'Customer',
            'company' => '',
            'street_address' => '123 Main St',
            'suburb' => '',
            'city' => 'Testville',
            'postcode' => '90210',
            'state' => 'CA',
            'zone_id' => 1,
            'country_id' => 223,
            'telephone' => '555-0100',
            'fax' => '',
        ];
    }

    public static function seedGuestCheckoutCart(bool $withShippingMethod = true): void
    {
        $pdo = Registry::get('PDO');
        $productId = (int) ($pdo->query('select products_id from osc_products limit 1')->fetchColumn() ?: 0);
        if ($productId < 1) {
            return;
        }

        $customer = Registry::get('Customer');
        if (!$customer->hasEmailAddress()) {
            $customer->setEmailAddress('coverage-guest@example.test');
        }

        $cart = Registry::get('ShoppingCart');
        if (!$cart->hasContents()) {
            $cart->reset();
            $cart->add($productId, 1);
        }

        $address = self::sampleAddress();
        if (!$cart->hasShippingAddress()) {
            $cart->setShippingAddress($address);
        }
        if (!$cart->hasBillingAddress()) {
            $cart->setBillingAddress($address);
        }

        if ($withShippingMethod && !$cart->hasShippingMethod()) {
            $cart->setShippingMethod([
                'id' => 'flat_flat',
                'title' => 'Flat Rate',
                'cost' => '5.00',
            ], false);
        }

        self::seedPaymentModules();
        self::seedBillingMethodWithoutRecalculate($cart);
    }

    public static function seedPaymentModules(): void
    {
        if (!Registry::exists('Payment')) {
            Registry::set('Payment', new Payment());
        }

        try {
            Registry::get('Payment')->loadAll();
        } catch (\Throwable) {
        }
    }

    public static function seedBillingMethodWithoutRecalculate(ShoppingCart $cart): void
    {
        if ($cart->hasBillingMethod()) {
            return;
        }

        try {
            $ref = new \ReflectionClass($cart);
            $prop = $ref->getProperty('_billing_method');
            $prop->setValue($cart, ['id' => 'cod_cod', 'title' => 'Cash On Delivery']);
        } catch (\Throwable) {
        }
    }

    public static function seedLoggedInCustomerIfAvailable(): void
    {
        $ids = ShopHarnessDataSeeder::ensureBaselineData();
        $customerId = $ids['customer_id'];
        if ($customerId < 1) {
            return;
        }

        $customer = Registry::get('Customer');
        $customer->setCustomerData($customerId);
        $_SESSION['osC_Customer_data']['id'] = $customerId;
    }
}
