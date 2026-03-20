<?php

declare(strict_types=1);

/**
 * Example: Using the osCommerce OM Registry for shared service access.
 *
 * The Registry is the primary dependency container in osCommerce OM. All
 * application services — DB connection, session, language, currencies — are
 * stored in the Registry and retrieved by name throughout the codebase.
 *
 * In production, services are registered during bootstrap. This example
 * shows the registration and retrieval pattern.
 */

// Autoload assumption: the OSCOM autoloader is active.
// require_once 'osCommerce/OM/Core/Registry.php'; // if running standalone

use osCommerce\OM\Core\Registry;

// 1. Register a service (normally done in bootstrap, not application code)
$db = new \stdClass(); // Replace with real DB connection object
$db->name = 'Main DB Connection';

Registry::set('Db', $db);

// 2. Retrieve the registered service anywhere in the application
$database = Registry::get('Db');
echo $database->name . "\n"; // "Main DB Connection"

// 3. Check existence before retrieval (avoids E_USER_NOTICE on miss)
if (Registry::exists('Session')) {
    $session = Registry::get('Session');
}

// 4. Force-replace an existing registration (use sparingly — e.g. for testing)
$newDb = new \stdClass();
$newDb->name = 'Test DB';
Registry::set('Db', $newDb, true); // $force = true

echo Registry::get('Db')->name . "\n"; // "Test DB"

// 5. Attempting to register a non-object triggers a PHP notice and returns false
$result = Registry::set('BadKey', 'this is a string');
// PHP Notice: OSC\OM\Registry::set - BadKey is not an object...
var_dump($result); // bool(false)
