<?php

declare(strict_types=1);

/**
 * Unit test bootstrap: OM autoloader (no LAMP / full OSCOM::initialize()).
 *
 * @see documents/test-suite-plan.md §3.1
 */

if (!defined('OSCOM_TIMESTAMP_START')) {
    define('OSCOM_TIMESTAMP_START', microtime());
}

require __DIR__ . '/support/Autoloader.php';

$OSCOM_Autoloader = new Autoloader('osCommerce\OM');
$OSCOM_Autoloader->register();
