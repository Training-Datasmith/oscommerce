<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * Discover Shop/Admin query routes for in-process rendering (PCOV).
 */
final class SiteRouteDiscovery
{
    /**
     * @return list<list<string>> GET key chains after the site key, e.g. ['Products','All']
     */
    public static function shopRoutes(): array
    {
        return self::discoverActionRoutes('Shop');
    }

    /**
     * @return list<list<string>> routes for Admin apps that use OM3 Application/*\Controller.php
     */
    public static function adminRoutes(): array
    {
        $routes = self::discoverActionRoutes('Admin');

        $extra = [
            ['Categories'],
            ['Customers'],
            ['Customers', 'Edit'],
            ['Configuration'],
            ['Languages'],
            ['Languages', 'Edit'],
            ['ZoneGroups'],
            ['ZoneGroups', 'EntriesEdit'],
            ['TaxClasses'],
            ['Currencies'],
            ['Administrators'],
            ['PaymentModules'],
            ['CoreUpdate'],
            ['ErrorLog'],
            ['CreditCards'],
            ['Countries'],
            ['Services'],
            ['Dashboard'],
        ];

        foreach ($extra as $parts) {
            $routes[] = $parts;
        }

        return self::uniqueRoutes($routes);
    }

    /**
     * @return list<list<string>>
     */
    public static function setupRoutes(): array
    {
        return [
            ['Index'],
            ['Install'],
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    public static function setupInstallStepParams(): array
    {
        return [
            [],
            ['step' => '2'],
            ['step' => '3'],
        ];
    }

    /**
     * @return list<list<string>>
     */
    private static function discoverActionRoutes(string $site): array
    {
        $base = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/' . $site . '/Application';
        $routes = [];

        foreach (glob($base . '/*', GLOB_ONLYDIR) ?: [] as $appDir) {
            $app = basename($appDir);
            if ($app === 'RPC' || !self::hasModernController($site, $app)) {
                continue;
            }

            $routes[] = [$app];

            $actionDir = $appDir . '/Action';
            if (!is_dir($actionDir)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($actionDir, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (!$file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $rel = substr($file->getPathname(), strlen($actionDir) + 1);
                $parts = explode('/', str_replace('\\', '/', preg_replace('/\.php$/', '', $rel)));
                if ($parts === ['']) {
                    continue;
                }

                $action = end($parts);
                if (in_array($action, ['Process', 'Delete'], true)) {
                    continue;
                }

                $routes[] = array_merge([$app], $parts);
            }
        }

        return self::uniqueRoutes($routes);
    }

    private static function hasModernController(string $site, string $application): bool
    {
        $path = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/' . $site . '/Application/' . $application . '/Controller.php';

        return is_file($path);
    }

    /**
     * @param list<list<string>> $routes
     * @return list<list<string>>
     */
    private static function uniqueRoutes(array $routes): array
    {
        $seen = [];
        $out = [];

        foreach ($routes as $parts) {
            $key = implode("\0", $parts);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $out[] = $parts;
        }

        return $out;
    }
}
