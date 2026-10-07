<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * Minimal HPDL template/box stubs for oscom.php legacy branches (PCOV).
 */
final class LegacyOscomHpdlStubs
{
    public static function template(string $pageFilename, string $group = 'Index'): object
    {
        return new class ($pageFilename, $group) {
            public function __construct(private string $page, private string $group)
            {
            }

            public function getCode(): string
            {
                return 'legacy_hpdl';
            }

            public function getGroup(): string
            {
                return $this->group;
            }

            public function getPageContentsFilename(): string
            {
                return $this->page;
            }
        };
    }

    public static function box(string $code = 'Cart'): object
    {
        return new class ($code) {
            public function __construct(private string $code)
            {
            }

            public function getCode(): string
            {
                return $this->code;
            }
        };
    }
}
