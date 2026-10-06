<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Hit discovered Application Action routes for Shop and Admin (HTTP line coverage).
 *
 * @group integration
 * @group apache
 */
#[Group('integration')]
#[Group('apache')]
class AllActionsHttpTest extends TestCase
{
    private function baseUrl(): string
    {
        return rtrim(getenv('OSCOMMERCE_BASE_URL') ?: 'http://localhost', '/');
    }

    public function testShopAndAdminActionRoutes(): void
    {
        $cookie = sys_get_temp_dir() . '/osc_actions_cookies.txt';
        @unlink($cookie);
        $this->adminLogin($cookie);

        $urls = array_merge(
            $this->discoverActionUrls('Shop'),
            $this->discoverActionUrls('Admin')
        );

        $this->assertGreaterThan(30, count($urls));

        foreach ($urls as $url) {
            $code = $this->getCode($url, $cookie);
            $this->assertLessThan(500, $code, $url);
        }
    }

    /**
     * @return list<string>
     */
    private function discoverActionUrls(string $site): array
    {
        $base = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/' . $site . '/Application';
        $urls = [];

        foreach (glob($base . '/*', GLOB_ONLYDIR) ?: [] as $appDir) {
            $app = basename($appDir);
            if ($app === 'RPC') {
                continue;
            }

            $urls[] = $this->baseUrl() . '/index.php?' . $site . '&' . $app;

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
                $action = str_replace(['/', '\\'], '&', preg_replace('/\.php$/', '', $rel));
                if (str_ends_with($action, 'Process') || str_ends_with($action, 'Delete')) {
                    continue;
                }
                $urls[] = $this->baseUrl() . '/index.php?' . $site . '&' . $app . '&' . $action;
            }
        }

        return array_values(array_unique($urls));
    }

    private function adminLogin(string $cookie): void
    {
        $ch = curl_init($this->baseUrl() . '/index.php?Admin&Login&action=process');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'username' => getenv('OSCOMMERCE_ADMIN_USER') ?: 'admin',
                'password' => getenv('OSCOMMERCE_ADMIN_PASS') ?: 'adminpass123',
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR => $cookie,
            CURLOPT_COOKIEFILE => $cookie,
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    private function getCode(string $url, string $cookie): int
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_COOKIEJAR => $cookie,
            CURLOPT_COOKIEFILE => $cookie,
        ]);
        curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $code;
    }
}
