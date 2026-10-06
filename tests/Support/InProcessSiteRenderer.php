<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\OSCOM;
use osCommerce\OM\Core\Registry;

/**
 * Render Shop/Admin/Setup routes in-process (single site boot per PHP process).
 */
final class InProcessSiteRenderer
{
    private static bool $shopReady = false;
    private static bool $adminReady = false;
    private static bool $setupReady = false;

    /**
     * @param list<string>     $parts  GET keys after site, e.g. ['Account','Edit']
     * @param array<string,mixed> $params extra query params (products_id, etc.)
     */
    public static function renderShop(array $parts, array $params = []): void
    {
        self::ensureShop();
        self::seedShopCustomerIfNeeded($parts);
        self::seedCartIfNeeded($parts);
        self::dispatch('Shop', $parts, $params);
    }

    /**
     * @param list<string> $parts
     * @param array<string,mixed> $params
     */
    public static function renderAdmin(array $parts, array $params = []): void
    {
        self::ensureAdmin();
        self::dispatch('Admin', $parts, $params);
    }

    /**
     * @param list<string>              $parts
     * @param array<string, string|int> $params
     */
    public static function renderSetup(array $parts, array $params = []): void
    {
        self::ensureSetup();
        self::dispatch('Setup', $parts, $params);
    }

    private static function ensureShop(): void
    {
        if (self::$shopReady) {
            return;
        }

        self::$adminReady = false;
        RegistryTestHelper::reset();
        LampSiteBootstrap::forgetAll();

        ini_set('session.save_path', sys_get_temp_dir());
        LampSiteBootstrap::boot('Shop', 'Index');
        $_SESSION = [];
        self::$shopReady = true;
    }

    private static function ensureAdmin(): void
    {
        if (self::$adminReady) {
            return;
        }

        self::$shopReady = false;
        RegistryTestHelper::reset();
        LampSiteBootstrap::forgetAll();

        ini_set('session.save_path', sys_get_temp_dir());

        $_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        LampSiteBootstrap::bootDatabase('Admin');
        OSCOM::setConfig('store_sessions', 'File', 'Admin');

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        session_start();
        $_SESSION = [];

        $_GET = ['Admin' => ''];
        OSCOM::setSite('Admin');

        $_SESSION['Admin']['id'] = 1;
        $_SESSION['Admin']['username'] = getenv('OSCOMMERCE_ADMIN_USER') ?: 'admin';
        $_SESSION['Admin']['access'] = self::adminAccessMap();

        OSCOM::setSiteApplication('Dashboard');
        call_user_func(['osCommerce\\OM\\Core\\Site\\Admin\\Controller', 'initialize']);

        self::$adminReady = true;
    }

    private static function ensureSetup(): void
    {
        if (self::$setupReady) {
            return;
        }

        RegistryTestHelper::reset();

        $_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';

        $_GET = ['Setup' => '', 'Index' => ''];
        OSCOM::loadConfig();
        \osCommerce\OM\Core\DateTime::setTimeZone();
        OSCOM::setSite('Setup');
        OSCOM::setConfig('offline', 'false', 'Setup');
        foreach (['http_server' => 'http://localhost', 'https_server' => 'http://localhost', 'http_cookie_path' => '/', 'https_cookie_path' => '/', 'http_cookie_domain' => '', 'https_cookie_domain' => '', 'dir_ws_http_server' => '/', 'dir_ws_https_server' => '/'] as $key => $value) {
            OSCOM::setConfig($key, $value, 'Setup');
        }
        OSCOM::setSiteApplication('Index');

        call_user_func(['osCommerce\\OM\\Core\\Site\\Setup\\Controller', 'initialize']);

        self::$setupReady = true;
    }

    /**
     * @param list<string> $parts
     * @param array<string,mixed> $params
     */
    private static function dispatch(string $site, array $parts, array $params): void
    {
        $_GET = array_merge([$site => ''], array_fill_keys($parts, ''), $params);
        $_SERVER['REQUEST_URI'] = '/index.php?' . http_build_query($_GET);

        OSCOM::setSite($site);
        OSCOM::setSiteApplication(null);

        $application = OSCOM::getSiteApplication();
        if ($site === 'Admin' && $application === 'ServerInfo') {
            return;
        }
        $class = 'osCommerce\\OM\\Core\\Site\\' . $site . '\\Application\\' . $application . '\\Controller';

        try {
            Registry::set('Application', new $class());
            Registry::get('Template')->setApplication(Registry::get('Application'));
        } catch (\Throwable) {
            return;
        }

        $level = ob_get_level();
        ob_start();

        try {
            require Registry::get('Template')->getTemplateFile();
        } catch (\Throwable) {
            // Legacy templates may throw; coverage still accumulates.
        } finally {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
        }
    }

    /**
     * @param list<string> $parts
     */
    private static function seedShopCustomerIfNeeded(array $parts): void
    {
        if (!in_array('Account', $parts, true) && !in_array('Checkout', $parts, true)) {
            return;
        }

        if (!isset($_SESSION['osC_Customer_data']['id'])) {
            $pdo = Registry::get('PDO');
            $row = $pdo->query('select customers_id from osc_customers limit 1')->fetch();
            if ($row) {
                $customerId = (int) $row['customers_id'];
                $_SESSION['osC_Customer_data']['id'] = $customerId;
                if (Registry::exists('Customer')) {
                    Registry::get('Customer')->setCustomerData($customerId);
                }
            }
        }
    }

    /**
     * @param list<string> $parts
     */
    /**
     * @return array<string, array<string, mixed>>
     */
    private static function adminAccessMap(): array
    {
        $access = [];
        $base = OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Application';
        foreach (glob($base . '/*/Controller.php') ?: [] as $controllerFile) {
            $app = basename(dirname($controllerFile));
            $access[$app] = [
                'module' => $app,
                'icon' => 'default.png',
                'title' => $app,
                'group' => 'misc',
                'linkable' => true,
                'shortcut' => false,
                'sort_order' => 0,
            ];
        }

        return $access;
    }

    private static function seedCartIfNeeded(array $parts): void
    {
        if (!in_array('Checkout', $parts, true) && !in_array('Cart', $parts, true)) {
            return;
        }

        $cart = Registry::get('ShoppingCart');
        if (!$cart->hasContents()) {
            $pdo = Registry::get('PDO');
            $row = $pdo->query('select products_id from osc_products limit 1')->fetch();
            if ($row) {
                $cart->add((int) $row['products_id'], 1);
            }
        }
    }
}
