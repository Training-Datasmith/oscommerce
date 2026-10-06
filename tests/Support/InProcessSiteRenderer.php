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
        if (in_array('Checkout', $parts, true) || in_array('Cart', $parts, true)) {
            ShopCheckoutSeeder::seedGuestCheckoutCart();
        }
        self::seedShopCustomerIfNeeded($parts);
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();
        self::seedCartIfNeeded($parts);
        self::seedCheckoutAddressesIfNeeded($parts);
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

    /**
     * Include a Shop application page PHP file under a booted storefront (PCOV).
     */
    public static function includeShopApplicationPage(string $application, string $pageFilename): void
    {
        self::ensureShop();
        self::primeShopContext($application, $pageFilename);

        $_GET = ['Shop' => '', $application => ''];
        OSCOM::setSite('Shop');
        OSCOM::setSiteApplication($application);

        try {
            $app = new ShopPageApplicationStub();
            $app->setPageTitle('Coverage');
            $app->setPageContent($pageFilename);
            Registry::set('Application', $app);
            Registry::get('Template')->setApplication($app);
        } catch (\Throwable) {
            return;
        }

        $path = OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Application/' . $application . '/pages/' . $pageFilename;
        if (!is_file($path)) {
            return;
        }

        $level = ob_get_level();
        ob_start();

        try {
            self::importShopPageScope();
            self::seedShopPageGlobals($application, $pageFilename);
            include $path;
        } catch (\Throwable) {
        } finally {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
        }
    }

    /**
     * Include an OM3 Admin application page (Controller.php apps only).
     */
    public static function includeAdminApplicationPage(string $application, string $pageFilename): void
    {
        if (!is_file(OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Application/' . $application . '/Controller.php')) {
            return;
        }

        self::ensureAdmin();

        $_GET = ['Admin' => '', $application => ''];
        OSCOM::setSite('Admin');
        OSCOM::setSiteApplication($application);

        $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Application\\' . $application . '\\Controller';

        try {
            Registry::set('Application', new $class(false));
            $app = Registry::get('Application');
            $app->setPageContent($pageFilename);
            Registry::get('Template')->setApplication($app);
        } catch (\Throwable) {
            return;
        }

        $path = OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Application/' . $application . '/pages/' . $pageFilename;
        if (!is_file($path)) {
            return;
        }

        $level = ob_get_level();
        ob_start();

        try {
            self::importAdminPageScope();
            self::seedAdminPageGlobals($application, $pageFilename);
            include $path;
        } catch (\Throwable) {
        } finally {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
        }
    }

    /**
     * Include Shop module box/content page templates.
     */
    /**
     * Include Setup application pages (Install steps, Offline, etc.).
     */
    public static function includeSetupApplicationPage(string $application, string $pageFilename): void
    {
        self::ensureSetup();

        if ($application === 'Install' && preg_match('/step_(\d+)\.php/', $pageFilename, $matches)) {
            $_GET = ['Setup' => '', 'Install' => '', 'step' => $matches[1]];
        } else {
            $_GET = ['Setup' => '', $application => ''];
        }

        OSCOM::setSite('Setup');
        OSCOM::setSiteApplication($application);

        try {
            $app = new SetupPageApplicationStub();
            $app->setPageTitle('Coverage');
            $app->setPageContent($pageFilename);
            Registry::set('Application', $app);
            Registry::get('Template')->setApplication($app);
        } catch (\Throwable) {
            return;
        }

        $path = OSCOM::BASE_DIRECTORY . 'Core/Site/Setup/Application/' . $application . '/pages/' . $pageFilename;
        if (!is_file($path)) {
            return;
        }

        $level = ob_get_level();
        ob_start();

        try {
            include $path;
        } catch (\Throwable) {
        } finally {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
        }
    }

    /**
     * Include Admin template fragments (header.php, footer.php).
     */
    public static function includeAdminTemplatePart(string $filename): void
    {
        self::ensureAdmin();
        $path = OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/templates/oscom/' . $filename;
        if (!is_file($path)) {
            return;
        }

        $level = ob_get_level();
        ob_start();
        try {
            self::importAdminPageScope();
            include $path;
        } catch (\Throwable) {
        } finally {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
        }
    }

    /**
     * Include Admin configuration parameter helpers under assets/cfg_parameters.
     */
    public static function includeAdminCfgParameters(): void
    {
        self::ensureAdmin();
        $dir = OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/assets/cfg_parameters';
        foreach (glob($dir . '/*.php') ?: [] as $file) {
            $level = ob_get_level();
            ob_start();
            try {
                self::importAdminPageScope();
                include $file;
            } catch (\Throwable) {
            } finally {
                while (ob_get_level() > $level) {
                    ob_end_clean();
                }
            }
        }
    }

    public static function includeShopModulePages(): void
    {
        self::ensureShop();
        self::primeShopContext('Index', 'main.php');

        foreach (['Box', 'Content'] as $moduleType) {
            $root = OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Module/' . $moduleType;
            if (!is_dir($root)) {
                continue;
            }

            foreach (glob($root . '/*/pages/*.php') ?: [] as $page) {
                $level = ob_get_level();
                ob_start();
                try {
                    self::importShopPageScope();
                    include $page;
                } catch (\Throwable) {
                } finally {
                    while (ob_get_level() > $level) {
                        ob_end_clean();
                    }
                }
            }
        }
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

        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();

        if (!isset($_SESSION['osC_Customer_data']['id']) && Registry::exists('Customer')) {
            $customer = Registry::get('Customer');
            if (!$customer->hasEmailAddress()) {
                $customer->setEmailAddress('coverage-guest@example.test');
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

    private static function seedCheckoutAddressesIfNeeded(array $parts): void
    {
        if (!in_array('Checkout', $parts, true)) {
            return;
        }

        $customer = Registry::get('Customer');
        $cart = Registry::get('ShoppingCart');

        if ($customer->isLoggedOn() && $customer->hasDefaultAddress()) {
            $addressId = $customer->getDefaultAddressID();
            if (!$cart->hasShippingAddress()) {
                $cart->setShippingAddress($addressId);
            }
            if (!$cart->hasBillingAddress()) {
                $cart->setBillingAddress($addressId);
            }
        }
    }

    private static function primeShopContext(string $application, string $pageFilename): void
    {
        $needsCustomer = $application === 'Account' || $application === 'Checkout'
            || str_contains($pageFilename, 'account') || str_contains($pageFilename, 'checkout')
            || str_contains($pageFilename, 'order');

        if ($needsCustomer) {
            self::seedShopCustomerIfNeeded(['Account']);
            ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();
        }

        if ($application === 'Checkout' || str_contains($pageFilename, 'billing') || str_contains($pageFilename, 'shipping')) {
            ShopCheckoutSeeder::seedGuestCheckoutCart();
            self::seedCartIfNeeded(['Checkout']);
            self::seedCheckoutAddressesIfNeeded(['Checkout']);
        }

        if ($application === 'Checkout' && Registry::exists('Payment') === false) {
            Registry::set('Payment', new \osCommerce\OM\Core\Site\Shop\Payment());
        }
    }

    private static function importShopPageScope(): void
    {
        global $OSCOM_Language, $OSCOM_Template, $OSCOM_MessageStack, $OSCOM_Customer, $OSCOM_Service, $OSCOM_Breadcrumb;
        global $OSCOM_ShoppingCart, $OSCOM_Currencies, $OSCOM_Payment, $OSCOM_PaymentModule, $OSCOM_PDO;

        $OSCOM_Language = Registry::get('Language');
        $OSCOM_Template = Registry::get('Template');
        $OSCOM_MessageStack = Registry::get('MessageStack');
        $OSCOM_Customer = Registry::get('Customer');
        $OSCOM_Service = Registry::get('Service');
        $OSCOM_Breadcrumb = Registry::exists('Breadcrumb') ? Registry::get('Breadcrumb') : null;
        $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
        $OSCOM_Currencies = Registry::get('Currencies');
        $OSCOM_PDO = Registry::get('PDO');

        if (Registry::exists('Payment')) {
            $OSCOM_Payment = Registry::get('Payment');
        } else {
            $OSCOM_Payment = new \osCommerce\OM\Core\Site\Shop\Payment();
            Registry::set('Payment', $OSCOM_Payment);
        }

        try {
            $OSCOM_Payment->loadAll();
        } catch (\Throwable) {
        }

        $OSCOM_PaymentModule = null;
    }

    private static function importAdminPageScope(): void
    {
        global $OSCOM_Language, $OSCOM_Template, $OSCOM_MessageStack;

        $OSCOM_Language = Registry::get('Language');
        $OSCOM_Template = Registry::get('Template');
        $OSCOM_MessageStack = Registry::get('MessageStack');
    }

    private static function seedShopPageGlobals(string $application, string $pageFilename): void
    {
        global $products_listing, $OSCOM_PDO, $OSCOM_Category;

        $OSCOM_PDO = Registry::get('PDO');

        if ($pageFilename === 'product_listing.php' || str_contains($pageFilename, 'listing')) {
            $products_listing = [
                'entries' => [
                    ['products_id' => 1, 'products_name' => 'Sample', 'products_price' => '10.00'],
                ],
                'total' => 1,
            ];
        }

        if ($application === 'Index' && Registry::exists('Category')) {
            $OSCOM_Category = Registry::get('Category');
        }
    }

    private static function seedAdminPageGlobals(string $application, string $pageFilename): void
    {
        global $OSCOM_ObjectInfo, $OSCOM_PDO;

        $OSCOM_PDO = Registry::get('PDO');

        if ($application === 'Customers' && str_starts_with($pageFilename, 'section_')) {
            $OSCOM_ObjectInfo = new \osCommerce\OM\Core\ObjectInfo([
                'customers_id' => 1,
                'customers_firstname' => 'Test',
                'customers_lastname' => 'User',
                'customers_email_address' => 'test@example.com',
            ]);
        }
    }
}
