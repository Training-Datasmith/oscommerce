<?php

declare(strict_types=1);

/**
 * Automate Setup install wizard (RPC + step 3) with full sample data.
 *
 * @see documents/test-suite-plan.md §4
 */

$repoRoot = dirname(__DIR__);

$baseUrl = rtrim(getenv('OSCOMMERCE_BASE_URL') ?: 'http://localhost', '/');
$dbServer = getenv('OSCOMMERCE_DB_SERVER') ?: '127.0.0.1';
$dbUser = getenv('OSCOMMERCE_DB_USER') ?: 'oscommerce';
$dbPass = getenv('OSCOMMERCE_DB_PASS') ?: 'oscommerce';
$dbName = getenv('OSCOMMERCE_DB_NAME') ?: 'oscommerce_test';
$dbPort = getenv('OSCOMMERCE_DB_PORT') ?: '3306';
$dbClass = getenv('OSCOMMERCE_DB_CLASS') ?: 'MySQL_Standard';
$dbPrefix = getenv('OSCOMMERCE_DB_PREFIX') ?: 'osc_';
$timeZone = getenv('OSCOMMERCE_TIME_ZONE') ?: 'UTC';

$shopName = getenv('OSCOMMERCE_SHOP_NAME') ?: 'Test Shop';
$shopOwner = getenv('OSCOMMERCE_SHOP_OWNER') ?: 'Test Owner';
$shopEmail = getenv('OSCOMMERCE_SHOP_EMAIL') ?: 'owner@example.com';
$adminUser = getenv('OSCOMMERCE_ADMIN_USER') ?: 'admin';
$adminPass = getenv('OSCOMMERCE_ADMIN_PASS') ?: 'adminpass123';

function harness_log(string $message): void
{
    fwrite(STDERR, 'setup-install-harness: ' . $message . PHP_EOL);
}

function harness_http_post(string $url, array $fields): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($fields),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT => 600,
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($body === false) {
        throw new RuntimeException('HTTP request failed: ' . $err);
    }

    return ['code' => $code, 'body' => $body];
}

function harness_rpc(string $baseUrl, string $action, array $db, array $extra = []): void
{
    $url = $baseUrl . '/index.php?RPC&Setup&Install&' . $action;
    $fields = array_merge([
        'server' => $db['server'],
        'username' => $db['username'],
        'password' => $db['password'],
        'name' => $db['database'],
        'port' => $db['port'],
        'class' => $db['class'],
        'prefix' => $db['prefix'],
    ], $extra);

    $response = harness_http_post($url, $fields);

    if ($response['code'] !== 200) {
        throw new RuntimeException($action . ' HTTP ' . $response['code'] . ': ' . substr($response['body'], 0, 500));
    }

    $json = json_decode($response['body'], true);
    if (!is_array($json) || ($json['result'] ?? false) !== true) {
        $message = is_array($json) ? ($json['error_message'] ?? $response['body']) : $response['body'];
        throw new RuntimeException($action . ' failed: ' . $message);
    }
}

function harness_reset_database(string $dbName): void
{
    $sql = 'DROP DATABASE IF EXISTS `' . str_replace('`', '``', $dbName) . '`; CREATE DATABASE `' . str_replace('`', '``', $dbName) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;';
    $cmd = 'sudo mysql -e ' . escapeshellarg($sql);
    passthru($cmd, $exitCode);
    if ($exitCode !== 0) {
        throw new RuntimeException('Failed to reset database ' . $dbName);
    }
}

$db = [
    'server' => $dbServer,
    'username' => $dbUser,
    'password' => $dbPass,
    'database' => $dbName,
    'port' => $dbPort,
    'class' => $dbClass,
    'prefix' => $dbPrefix,
];

try {
    $setupIni = $repoRoot . '/tools/templates/settings-setup.ini';
    $targetIni = $repoRoot . '/osCommerce/OM/Config/settings.ini';
    if (!is_readable($setupIni)) {
        throw new RuntimeException('Missing template: ' . $setupIni);
    }
    $copyCmd = 'cp ' . escapeshellarg($setupIni) . ' ' . escapeshellarg($targetIni);
    passthru('sudo ' . $copyCmd, $copyExit);
    if ($copyExit !== 0) {
        throw new RuntimeException('Could not reset settings.ini for Setup');
    }

    harness_log('reset database ' . $dbName);
    harness_reset_database($dbName);

    harness_log('DBCheck');
    harness_rpc($baseUrl, 'DBCheck', $db);

    harness_log('DBImport (schema)');
    harness_rpc($baseUrl, 'DBImport', $db);

    harness_log('DBImportSample (full sample data)');
    harness_rpc($baseUrl, 'DBImportSample', $db);

    harness_log('DBConfigureShop');
    harness_rpc($baseUrl, 'DBConfigureShop', $db, [
        'shop_name' => $shopName,
        'shop_owner_name' => $shopOwner,
        'shop_owner_email' => $shopEmail,
        'admin_username' => $adminUser,
        'admin_password' => $adminPass,
    ]);

    harness_log('Setup step 3 (write settings.ini)');
    $step3 = harness_http_post($baseUrl . '/index.php?Setup&Install&step=3', [
        'HTTP_WWW_ADDRESS' => $baseUrl . '/',
        'DB_SERVER' => $dbServer,
        'DB_SERVER_USERNAME' => $dbUser,
        'DB_SERVER_PASSWORD' => $dbPass,
        'DB_DATABASE' => $dbName,
        'DB_SERVER_PORT' => $dbPort,
        'DB_DATABASE_CLASS' => $dbClass,
        'DB_TABLE_PREFIX' => $dbPrefix,
        'CFG_TIME_ZONE' => $timeZone,
        'CFG_STORE_NAME' => $shopName,
        'CFG_STORE_OWNER_NAME' => $shopOwner,
        'CFG_STORE_OWNER_EMAIL_ADDRESS' => $shopEmail,
        'CFG_ADMINISTRATOR_USERNAME' => $adminUser,
        'CFG_ADMINISTRATOR_PASSWORD' => $adminPass,
    ]);

    if ($step3['code'] !== 200 || stripos($step3['body'], 'successful') === false) {
        throw new RuntimeException('step 3 did not report success (HTTP ' . $step3['code'] . ')');
    }

    $envDir = $repoRoot . '/build';
    if (!is_dir($envDir)) {
        mkdir($envDir, 0775, true);
    }

    $envFile = $envDir . '/install.env';
    $envLines = [
        'OSCOMMERCE_BASE_URL=' . $baseUrl,
        'OSCOMMERCE_ADMIN_USER=' . $adminUser,
        'OSCOMMERCE_ADMIN_PASS=' . $adminPass,
        'OSCOMMERCE_DB_SERVER=' . $dbServer,
        'OSCOMMERCE_DB_NAME=' . $dbName,
    ];
    file_put_contents($envFile, implode("\n", $envLines) . "\n");

    harness_log('done; wrote ' . $envFile);
    exit(0);
} catch (Throwable $e) {
    harness_log('ERROR: ' . $e->getMessage());
    exit(1);
}
