<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\Site\Setup\Application\Install\Model\importDB;

/**
 * Harness-safe wrappers around importDB partial install steps (PCOV).
 */
final class ImportDbPartialRunner
{
    public static function installServiceAndModuleStack(): void
    {
        importDB::installServiceAndPaymentStack();
    }

    public static function runHarnessSafePostImport(string $tablePrefix = 'osc_'): void
    {
        importDB::executeHarnessSafePostImport($tablePrefix);
    }
}
