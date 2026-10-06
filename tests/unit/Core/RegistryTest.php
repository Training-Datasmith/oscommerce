<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use osCommerce\OM\Core\Registry;
use PHPUnit\Framework\TestCase;

class RegistryTest extends TestCase
{
    protected function tearDown(): void
    {
        if (Registry::exists('TestKey')) {
            // Registry has no remove; overwrite on next test via force if needed.
        }
    }

    public function testSetGetExists(): void
    {
        Registry::set('TestKey', 'value');
        $this->assertTrue(Registry::exists('TestKey'));
        $this->assertSame('value', Registry::get('TestKey'));
    }

    public function testPrefixedKeyNormalization(): void
    {
        Registry::set('OSCOM_Another', 42, true);
        $this->assertSame(42, Registry::get('Another'));
    }
}
