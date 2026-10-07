<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\OSCOM;
use Phar;

/**
 * Minimal update.phar for CoreUpdate model coverage (removed in tearDown).
 */
final class CoreUpdatePharFixture
{
    private static ?string $backupPath = null;

    public static function installMinimalPhar(): void
    {
        $dir = OSCOM::BASE_DIRECTORY . 'Work/CoreUpdate';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $target = $dir . '/update.phar';
        if (is_file($target)) {
            self::$backupPath = $dir . '/update.phar.coverage-backup';
            if (!is_file(self::$backupPath)) {
                copy($target, self::$backupPath);
            }
            unlink($target);
        }

        if (ini_get('phar.readonly')) {
            @ini_set('phar.readonly', '0');
        }

        if (class_exists(Phar::class, false) && !ini_get('phar.readonly')) {
            $phar = new Phar($target);
            $phar->addFromString('osCommerce/OM/Core/README-coverage.txt', 'coverage fixture');
            $phar->setMetadata([
                'version_to' => '9.9.9-coverage',
                'delete' => [],
            ]);
        }
    }

    public static function restore(): void
    {
        $target = OSCOM::BASE_DIRECTORY . 'Work/CoreUpdate/update.phar';
        if (is_file($target)) {
            @unlink($target);
        }
        if (self::$backupPath !== null && is_file(self::$backupPath)) {
            copy(self::$backupPath, $target);
            @unlink(self::$backupPath);
            self::$backupPath = null;
        }
    }
}
