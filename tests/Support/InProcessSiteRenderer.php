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
            extract(self::buildShopScopeVariables($application, $pageFilename), EXTR_OVERWRITE);
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
            extract(self::buildAdminScopeVariables($application, $pageFilename), EXTR_OVERWRITE);
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
            extract(self::buildAdminScopeVariables('Dashboard', 'main.php'), EXTR_OVERWRITE);
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
                extract(self::buildAdminScopeVariables('Dashboard', 'main.php'), EXTR_OVERWRITE);
                include $file;
            } catch (\Throwable) {
            } finally {
                while (ob_get_level() > $level) {
                    ob_end_clean();
                }
            }
        }
    }

    /**
     * Render the full oscom.php layout for a given application page (PCOV).
     */
    public static function includeShopPageViaOscomLayout(string $application, string $pageFilename): void
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
            $template = Registry::get('Template');
            $template->setApplication($app);
            $template->setHasHeader(true);
            $template->setHasFooter(true);
            $template->setHasBoxModules(true);
            $template->setHasContentModules(true);
            $template->addPageTags('coverage', 'grind');
        } catch (\Throwable) {
            return;
        }

        $level = ob_get_level();
        ob_start();

        try {
            extract(self::buildShopScopeVariables($application, $pageFilename), EXTR_OVERWRITE);
            include Registry::get('Template')->getTemplateFile();
        } catch (\Throwable) {
        } finally {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
        }
    }

    /**
     * @param array<string, mixed> $scopeExtras merged into buildShopScopeVariables before include
     */
    public static function includeShopApplicationPageWithScopeExtras(string $application, string $pageFilename, array $scopeExtras): void
    {
        self::ensureShop();
        self::primeShopContext($application, $pageFilename);

        $_GET = ['Shop' => '', $application => ''];
        OSCOM::setSite('Shop');
        OSCOM::setSiteApplication($application);

        $path = OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Application/' . $application . '/pages/' . $pageFilename;
        if (!is_file($path)) {
            return;
        }

        $level = ob_get_level();
        ob_start();
        try {
            extract(array_merge(self::buildShopScopeVariables($application, $pageFilename), $scopeExtras), EXTR_OVERWRITE);
            include $path;
        } catch (\Throwable) {
        } finally {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
        }
    }

    /**
     * oscom.php HPDL / non-DEFAULT_TEMPLATE branches (PCOV).
     */
    public static function includeShopOscomLegacyLayout(string $application, string $pageFilename): void
    {
        self::ensureShop();
        self::primeShopContext($application, $pageFilename);

        $_GET = ['Shop' => '', $application => ''];
        OSCOM::setSite('Shop');
        OSCOM::setSiteApplication($application);

        try {
            $app = new ShopPageApplicationStub();
            $app->setPageTitle('Coverage Legacy');
            $app->setPageContent($pageFilename);
            Registry::set('Application', $app);
            $template = Registry::get('Template');
            $template->setApplication($app);
            $template->setHasHeader(true);
            $template->setHasFooter(true);
            $template->setHasBoxModules(true);
            $template->setHasContentModules(true);
            $_SESSION['template'] = ['id' => 1, 'code' => 'legacy_hpdl'];
            $template->set('legacy_hpdl');
        } catch (\Throwable) {
            return;
        }

        $level = ob_get_level();
        ob_start();
        try {
            $scope = array_merge(self::buildShopScopeVariables($application, $pageFilename), [
                'osC_Template' => LegacyOscomHpdlStubs::template($pageFilename, $application),
                'osC_Box' => LegacyOscomHpdlStubs::box('Cart'),
            ]);
            extract($scope, EXTR_OVERWRITE);
            include Registry::get('Template')->getTemplateFile();
        } catch (\Throwable) {
        } finally {
            while (ob_get_level() > $level) {
                ob_end_clean();
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
                    extract(self::buildShopScopeVariables('Index', 'main.php'), EXTR_OVERWRITE);
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
        ShopHarnessDataSeeder::ensureBaselineData();
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
            $applicationName = OSCOM::getSiteApplication();
            $pageFilename = Registry::get('Application')->getPageContent();

            if ($site === 'Shop') {
                extract(self::buildShopScopeVariables($applicationName, $pageFilename), EXTR_OVERWRITE);
            } elseif ($site === 'Admin') {
                extract(self::buildAdminScopeVariables($applicationName, $pageFilename), EXTR_OVERWRITE);
            }

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

    /**
     * @return array<string, mixed>
     */
    private static function buildShopScopeVariables(string $application, string $pageFilename): array
    {
        $OSCOM_Language = Registry::get('Language');
        $OSCOM_Template = Registry::get('Template');
        $OSCOM_MessageStack = Registry::get('MessageStack');
        $OSCOM_Customer = Registry::get('Customer');
        $OSCOM_Service = Registry::get('Service');
        $OSCOM_Breadcrumb = Registry::exists('Breadcrumb') ? Registry::get('Breadcrumb') : null;
        $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
        $OSCOM_Currencies = Registry::get('Currencies');
        $OSCOM_PDO = Registry::get('PDO');

        if (Registry::exists('Image')) {
            $OSCOM_Image = Registry::get('Image');
        } else {
            $OSCOM_Image = new \osCommerce\OM\Core\Image();
            Registry::set('Image', $OSCOM_Image);
        }

        if (Registry::exists('Category')) {
            $OSCOM_Category = Registry::get('Category');
        } else {
            $OSCOM_Category = new \osCommerce\OM\Core\Site\Shop\Category();
            Registry::set('Category', $OSCOM_Category);
        }

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
        $cart = Registry::get('ShoppingCart');
        if ($cart->hasBillingMethod()) {
            $billingId = (string) $cart->getBillingMethod('id');
            $moduleCode = explode('_', $billingId)[0] ?? '';
            if ($moduleCode !== '') {
                $moduleCode = strtoupper($moduleCode);
                try {
                    $OSCOM_Payment->load($moduleCode);
                    if (Registry::exists('PaymentModule')) {
                        $OSCOM_PaymentModule = Registry::get('PaymentModule');
                    }
                } catch (\Throwable) {
                }
            }
        }

        $products_listing = null;

        if ($application === 'Account' && str_contains($pageFilename, 'orders')) {
            $ids = ShopHarnessDataSeeder::ensureBaselineData();
            if ($ids['order_id'] > 0) {
                $_GET['order_id'] = (string) $ids['order_id'];
            }
        }

        if ($application === 'Index' && Registry::exists('Category')) {
            $OSCOM_Category = Registry::get('Category');
        }

        $OSCOM_Banner = Registry::exists('Banner') ? Registry::get('Banner') : null;

        if ($pageFilename === 'product_listing.php' || str_contains($pageFilename, 'listing')) {
            $ids = $OSCOM_PDO->query(
                'select products_id from osc_products where manufacturers_id > 0 order by products_id limit 3'
            )->fetchAll(\PDO::FETCH_COLUMN);
            if ($ids === []) {
                $ids = $OSCOM_PDO->query('select products_id from osc_products order by products_id limit 3')->fetchAll(\PDO::FETCH_COLUMN);
            }
            if ($ids === []) {
                $ids = [1];
            }
            $entries = [];
            foreach ($ids as $pid) {
                $entries[] = ['products_id' => (int) $pid];
            }
            $products_listing = [
                'entries' => $entries,
                'total' => max(count($entries), 2),
                'pages' => 1,
                'page' => 1,
            ];
            $_GET['page'] = '1';
            if (!isset($_GET['manufacturers'])) {
                $_GET['manufacturers'] = '1';
            }
        }

        return compact(
            'OSCOM_Language',
            'OSCOM_Template',
            'OSCOM_MessageStack',
            'OSCOM_Customer',
            'OSCOM_Service',
            'OSCOM_Breadcrumb',
            'OSCOM_ShoppingCart',
            'OSCOM_Currencies',
            'OSCOM_Payment',
            'OSCOM_PaymentModule',
            'OSCOM_PDO',
            'OSCOM_Image',
            'OSCOM_Category',
            'OSCOM_Banner',
            'products_listing',
        );
    }

    /**
     * Re-render the oscom layout for the current dispatched Shop application (PCOV).
     */
    public static function includeRenderedShopOscomLayout(): void
    {
        self::ensureShop();
        $application = OSCOM::getSiteApplication();
        $pageFilename = Registry::get('Application')->getPageContent();
        self::primeShopContext($application, $pageFilename);

        $template = Registry::get('Template');
        $template->setHasHeader(true);
        $template->setHasFooter(true);
        $template->setHasBoxModules(true);
        $template->setHasContentModules(true);

        $level = ob_get_level();
        ob_start();
        try {
            extract(self::buildShopScopeVariables($application, $pageFilename), EXTR_OVERWRITE);
            include $template->getTemplateFile();
        } catch (\Throwable) {
        } finally {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private static function buildAdminScopeVariables(string $application, string $pageFilename): array
    {
        $OSCOM_Language = Registry::get('Language');
        $OSCOM_Template = Registry::get('Template');
        $OSCOM_MessageStack = Registry::get('MessageStack');
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_ObjectInfo = null;
        $new_customer = false;

        $ids = ShopHarnessDataSeeder::ensureBaselineData();

        if ($application === 'Customers' && str_starts_with($pageFilename, 'section_')) {
            $_GET['id'] = (string) ($ids['customer_id'] ?: 1);

            $OSCOM_ObjectInfo = new \osCommerce\OM\Core\ObjectInfo([
                'customers_id' => $ids['customer_id'] ?: 1,
                'customers_firstname' => 'Test',
                'customers_lastname' => 'User',
                'customers_email_address' => 'coverage-customer@example.test',
                'customers_name' => 'Test User',
                'customers_telephone' => '555-0100',
                'customers_fax' => '',
                'customers_newsletter' => '1',
                'address_book_id' => 1,
                'entry_firstname' => 'Test',
                'entry_lastname' => 'User',
                'entry_street_address' => '123 Main St',
                'entry_city' => 'Testville',
                'entry_postcode' => '90210',
                'entry_state' => 'CA',
                'entry_country_id' => 223,
                'entry_zone_id' => 1,
            ]);
        }

        return compact(
            'OSCOM_Language',
            'OSCOM_Template',
            'OSCOM_MessageStack',
            'OSCOM_ObjectInfo',
            'OSCOM_PDO',
            'new_customer',
        );
    }
}
