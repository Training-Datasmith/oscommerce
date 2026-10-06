<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\Registry;

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
    }

    public static function seedLoggedInCustomerIfAvailable(): void
    {
        $pdo = Registry::get('PDO');
        $customerId = (int) ($pdo->query('select customers_id from osc_customers limit 1')->fetchColumn() ?: 0);
        if ($customerId < 1) {
            return;
        }

        $customer = Registry::get('Customer');
        $customer->setCustomerData($customerId);
        $_SESSION['osC_Customer_data']['id'] = $customerId;
    }
}
