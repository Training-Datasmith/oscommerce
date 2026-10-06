<?php

declare(strict_types=1);

/**
 * Integration bootstrap: shared bootstrap (unit + Composer + optional install.env).
 */

require __DIR__ . '/bootstrap.php';

if (!\defined('OSCOM_TEST_REDIRECT_THROW')) {
    \define('OSCOM_TEST_REDIRECT_THROW', true);
}

if (!\defined('OSCOM_TEST_SKIP_MAIL')) {
    \define('OSCOM_TEST_SKIP_MAIL', true);
}
