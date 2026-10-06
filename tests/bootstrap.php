<?php

declare(strict_types=1);

/**
 * Shared test bootstrap (stub). Phase 0 implements autoload + shared ini.
 *
 * @see documents/test-suite-plan.md
 */

require __DIR__ . '/bootstrap-unit.php';

require dirname(__DIR__) . '/vendor/autoload.php';

$installEnv = dirname(__DIR__) . '/build/install.env';
if (is_readable($installEnv)) {
    foreach (file($installEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}
