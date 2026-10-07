<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Payment;
use osCommerce\OM\Core\Site\Shop\Shipping;
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
        if (!isset($_SESSION['currency']) && \defined('DEFAULT_CURRENCY')) {
            $_SESSION['currency'] = DEFAULT_CURRENCY;
        }

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

        $cart->setBillingMethod([
            'id' => 'cod_cod',
            'title' => 'Cash On Delivery',
            'module' => 'Cash On Delivery',
        ], false);
    }

    /**
     * Register Shipping in Registry with quotes for checkout templates (PCOV).
     */
    public static function ensureShopShippingModules(): void
    {
        $pdo = Registry::get('PDO');
        $count = (int) $pdo->query("select count(*) from osc_modules where modules_group = 'Shipping'")->fetchColumn();
        if ($count > 0) {
            return;
        }

        $pdo->prepare(
            'insert into osc_modules (title, code, author_name, author_www, modules_group) values (:title, :code, :author_name, :author_www, :group)'
        )->execute([
            ':title' => 'Flat Rate',
            ':code' => 'Flat',
            ':author_name' => 'osCommerce',
            ':author_www' => 'http://www.oscommerce.com',
            ':group' => 'Shipping',
        ]);
    }

    public static function ensureShippingWithQuotes(bool $presetCartMethod = true): void
    {
        self::ensureShopShippingModules();
        self::seedGuestCheckoutCart(false);
        self::seedPaymentModules();

        $cart = Registry::get('ShoppingCart');
        if (!$cart->hasShippingAddress()) {
            $cart->setShippingAddress(self::sampleAddress());
        }

        if (isset($_SESSION['osC_ShoppingCart_data']['shipping_quotes'])) {
            unset($_SESSION['osC_ShoppingCart_data']['shipping_quotes']);
        }

        $shipping = new Shipping();
        Registry::set('Shipping', $shipping, true);

        if ($presetCartMethod && $shipping->hasQuotes()) {
            $cheapest = $shipping->getCheapestQuote();
            if (!empty($cheapest['id'])) {
                $cart->setShippingMethod($cheapest, false);
            }
        }

        $_SESSION['comments'] = 'Coverage checkout comment';
    }

    /**
     * Two shipping methods in session so checkout shipping.php hits multi-quote UI branches.
     */
    public static function ensureMultiMethodShippingQuotes(): void
    {
        self::ensureShopShippingModules();
        self::seedGuestCheckoutCart(false);

        $cart = Registry::get('ShoppingCart');
        if (!$cart->hasShippingAddress()) {
            $cart->setShippingAddress(self::sampleAddress());
        }

        $_SESSION['osC_ShoppingCart_data']['shipping_quotes'] = [
            [
                'id' => 'flat',
                'module' => 'Flat Rate (Coverage)',
                'methods' => [
                    ['id' => 'flat', 'title' => 'Standard', 'cost' => '5.0000'],
                    ['id' => 'express', 'title' => 'Express', 'cost' => '12.0000'],
                ],
                'tax_class_id' => defined('MODULE_SHIPPING_FLAT_TAX_CLASS') ? MODULE_SHIPPING_FLAT_TAX_CLASS : 0,
            ],
        ];

        $cart->setShippingMethod([
            'id' => 'flat_flat',
            'title' => 'Flat Rate (Coverage) (Standard)',
            'cost' => '5.0000',
        ], false);

        Registry::set('Shipping', new Shipping(), true);
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
