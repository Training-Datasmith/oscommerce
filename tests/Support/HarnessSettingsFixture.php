<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * Canonical settings.ini for integration tests (matches setup-install-harness defaults).
 */
final class HarnessSettingsFixture
{
    public static function contents(): string
    {
        $dbServer = getenv('OSCOMMERCE_DB_SERVER') ?: '127.0.0.1';
        $dbUser = getenv('OSCOMMERCE_DB_USER') ?: 'oscommerce';
        $dbPass = getenv('OSCOMMERCE_DB_PASS') ?: 'oscommerce';
        $dbName = getenv('OSCOMMERCE_DB_NAME') ?: 'oscommerce_test';
        $dbPort = getenv('OSCOMMERCE_DB_PORT') ?: '3306';
        $dbPrefix = getenv('OSCOMMERCE_DB_PREFIX') ?: 'osc_';
        $timeZone = getenv('OSCOMMERCE_TIME_ZONE') ?: 'UTC';
        $baseUrl = rtrim(getenv('OSCOMMERCE_BASE_URL') ?: 'http://localhost', '/');
        $publicRoot = dirname(__DIR__, 2) . '/';

        return <<<INI
[OSCOM]
bootstrap_file = "index.php"
default_site = "Shop"
time_zone = "{$timeZone}"
dir_fs_public = "{$publicRoot}public/"

[Admin]
enable_ssl = "false"
http_server = "{$baseUrl}"
https_server = "{$baseUrl}"
http_cookie_domain = ""
https_cookie_domain = ""
http_cookie_path = "/"
https_cookie_path = "/"
dir_ws_http_server = "/"
dir_ws_https_server = "/"
db_server = "{$dbServer}"
db_server_username = "{$dbUser}"
db_server_password = "{$dbPass}"
db_server_port = "{$dbPort}"
db_database = "{$dbName}"
db_driver = "MySQL\\Standard"
db_table_prefix = "{$dbPrefix}"
db_server_persistent_connections = "false"
store_sessions = "Database"

[Shop]
enable_ssl = "false"
http_server = "{$baseUrl}"
https_server = "{$baseUrl}"
http_cookie_domain = ""
https_cookie_domain = ""
http_cookie_path = "/"
https_cookie_path = "/"
dir_ws_http_server = "/"
dir_ws_https_server = "/"
product_images_http_server = ""
product_images_https_server = ""
product_images_dir_ws_http_server = "/public/products/"
product_images_dir_ws_https_server = "/public/products/"
db_server = "{$dbServer}"
db_server_username = "{$dbUser}"
db_server_password = "{$dbPass}"
db_server_port = "{$dbPort}"
db_database = "{$dbName}"
db_driver = "MySQL\\Standard"
db_table_prefix = "{$dbPrefix}"
db_server_persistent_connections = "false"
store_sessions = "Database"

[Setup]
offline = "true"
INI;
    }

    public static function restoreToConfigFile(): void
    {
        $path = dirname(__DIR__, 2) . '/osCommerce/OM/Config/settings.ini';
        @chmod($path, 0666);
        file_put_contents($path, self::contents());
        @chmod($path, 0664);
    }
}
