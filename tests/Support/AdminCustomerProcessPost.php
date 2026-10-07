<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * Valid POST payloads for Customers Save/Process integration tests.
 */
final class AdminCustomerProcessPost
{
    /**
     * @return array<string, mixed>
     */
    public static function customerFields(string $first, string $last, string $email, string $password = ''): array
    {
        return [
            'gender' => 'm',
            'firstname' => $first,
            'lastname' => $last,
            'dob' => '1990-06-15',
            'email_address' => $email,
            'password' => $password,
            'confirmation' => $password,
            'newsletter' => 'on',
            'status' => 'on',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function addressFields(int $addressId, bool $changed = true): array
    {
        return [
            'id' => (string) $addressId,
            'gender' => 'f',
            'firstname' => 'Coverage',
            'lastname' => 'Address',
            'company' => 'Acme',
            'street_address' => '456 Oak Ave',
            'suburb' => '',
            'city' => 'Testville',
            'postcode' => '90211',
            'state' => 'CA',
            'zone_id' => '1',
            'country_id' => '223',
            'telephone' => '555-0200',
            'fax' => '555-0201',
            'changed' => $changed,
        ];
    }
}
