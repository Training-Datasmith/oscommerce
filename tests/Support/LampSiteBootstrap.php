<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\Cache;
use osCommerce\OM\Core\DateTime;
use osCommerce\OM\Core\OSCOM;
use osCommerce\OM\Core\PDO;
use osCommerce\OM\Core\Registry;

/**
 * Boot an installed site (Shop/Admin) in-process with real PDO (requires harness).
 */
final class LampSiteBootstrap
{
    private static array $booted = [];

    public static function forgetAll(): void
    {
        self::$booted = [];
    }

    /**
     * PDO + table prefix only (no session/HTTP), for SQL procedure sweeps.
     */
    public static function bootDatabase(string $site): void
    {
        $key = 'db:' . $site;
        if (isset(self::$booted[$key]) && Registry::exists('PDO')) {
            return;
        }

        RegistryTestHelper::reset();

        OSCOM::loadConfig();
        DateTime::setTimeZone();
        OSCOM::setSite($site);

        if (OSCOM::configExists('db_table_prefix', $site)) {
            OSCOM::setConfig('db_table_prefix', OSCOM::getConfig('db_table_prefix', $site), $site);
        }

        Registry::set('Cache', new Cache(), true);
        Registry::set('PDO', PDO::initialize(), true);

        self::$booted[$key] = true;
    }

    public static function boot(string $site, ?string $application = null): void
    {
        $key = $site . ':' . ($application ?? '');
        if (isset(self::$booted[$key]) && Registry::exists('PDO')) {
            return;
        }

        RegistryTestHelper::reset();

        $_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'] ?? 'osCommerce-Coverage-Sweep/1.0';
        $_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/index.php?' . $site;

        ini_set('session.save_path', sys_get_temp_dir());

        $_GET = [$site => ''];
        if ($application !== null && $application !== '') {
            $_GET[$application] = '';
        }

        OSCOM::loadConfig();
        DateTime::setTimeZone();
        OSCOM::setSite($site);
        OSCOM::setConfig('store_sessions', 'File', $site);
        OSCOM::setSiteApplication($application);

        call_user_func(['osCommerce\\OM\\Core\\Site\\' . $site . '\\Controller', 'initialize']);

        self::$booted[$key] = true;
    }
}
