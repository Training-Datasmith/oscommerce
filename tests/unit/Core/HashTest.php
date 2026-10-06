<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use osCommerce\OM\Core\Hash;
use PHPUnit\Framework\TestCase;

class HashTest extends TestCase
{
    public function testGetAndValidateRoundTrip(): void
    {
        $plain = 'training-datasmith';
        $hashed = Hash::get($plain);

        $this->assertNotSame($plain, $hashed);
        $this->assertTrue(Hash::validate($plain, $hashed));
        $this->assertFalse(Hash::validate('wrong', $hashed));
    }

    public function testGetRandomStringTypes(): void
    {
        $this->assertSame(8, strlen(Hash::getRandomString(8, 'digits')));
        $this->assertMatchesRegularExpression('/^\d{8}$/', Hash::getRandomString(8, 'digits'));

        $this->assertSame(12, strlen(Hash::getRandomString(12, 'chars')));
        $this->assertFalse(Hash::getRandomString(5, 'invalid'));
    }
}
