<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * Valid $_POST for Checkout Address Process actions.
 */
final class CheckoutProcessPost
{
    /**
     * @return array<string, string|int>
     */
    public static function validAddress(): array
    {
        $address = ShopCheckoutSeeder::sampleAddress();
        $zoneCode = 'CA';
        try {
            if (\osCommerce\OM\Core\Registry::exists('PDO')) {
                $pdo = \osCommerce\OM\Core\Registry::get('PDO');
                $zoneCode = (string) ($pdo->query('select zone_code from osc_zones where zone_country_id = 223 limit 1')->fetchColumn() ?: 'CA');
            }
        } catch (\Throwable) {
        }

        return [
            'gender' => 'm',
            'firstname' => $address['firstname'],
            'lastname' => $address['lastname'],
            'company' => $address['company'],
            'street_address' => $address['street_address'],
            'suburb' => $address['suburb'],
            'city' => $address['city'],
            'postcode' => $address['postcode'],
            'state' => $zoneCode,
            'country' => (string) $address['country_id'],
            'telephone' => $address['telephone'],
            'fax' => $address['fax'],
            'zone_id' => (string) $address['zone_id'],
        ];
    }
}
