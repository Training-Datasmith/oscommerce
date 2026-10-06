<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\DateTime;
use osCommerce\OM\Core\OSCOM;
use osCommerce\OM\Core\Registry;

/**
 * Minimal Shop site context for HTML unit tests (no database).
 */
final class ShopHtmlFixture
{
    private static bool $booted = false;

    public static function boot(): void
    {
        if (self::$booted) {
            return;
        }

        OSCOM::loadConfig();
        DateTime::setTimeZone();
        OSCOM::setSite('Shop');

        Registry::set('Template', new class () {
            public function getCode(?int $id = null): string
            {
                return 'oscom';
            }
        });

        Registry::set('Session', new class () {
            public function hasStarted(): bool
            {
                return false;
            }

            public function getName(): string
            {
                return 'osCsid';
            }

            public function getID(): string
            {
                return '';
            }
        });

        self::$booted = true;
    }
}
