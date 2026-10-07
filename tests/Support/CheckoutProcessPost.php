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

        return [
            'gender' => 'm',
            'firstname' => $address['firstname'],
            'lastname' => $address['lastname'],
            'company' => $address['company'],
            'street_address' => $address['street_address'],
            'suburb' => $address['suburb'],
            'city' => $address['city'],
            'postcode' => $address['postcode'],
            'state' => 'CA',
            'country' => (string) $address['country_id'],
            'telephone' => $address['telephone'],
            'fax' => $address['fax'],
            'zone_id' => (string) $address['zone_id'],
        ];
    }
}
