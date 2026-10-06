<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Hit every Shop/Admin application entry point over HTTP (installed store).
 *
 * @group integration
 * @group apache
 */
#[Group('integration')]
#[Group('apache')]
class HttpApplicationMatrixTest extends TestCase
{
    private function baseUrl(): string
    {
        return rtrim(getenv('OSCOMMERCE_BASE_URL') ?: 'http://localhost', '/');
    }

    public function testAllApplicationUrlsReturnNon500(): void
    {
        $cookieFile = sys_get_temp_dir() . '/oscommerce_matrix_cookies.txt';
        @unlink($cookieFile);

        $this->adminLogin($cookieFile);

        $urls = array_merge(
            $this->discoverApplicationUrls('Shop'),
            $this->discoverApplicationUrls('Admin')
        );

        $this->assertGreaterThan(20, count($urls));

        foreach ($urls as $url) {
            $code = $this->httpGetCode($url, $cookieFile);
            $this->assertLessThan(
                500,
                $code,
                'HTTP ' . $code . ' for ' . $url
            );
        }
    }

    /**
     * @return list<string>
     */
    private function discoverApplicationUrls(string $site): array
    {
        $base = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/' . $site . '/Application';
        $urls = [$this->baseUrl() . '/index.php?' . $site];

        foreach (glob($base . '/*', GLOB_ONLYDIR) ?: [] as $dir) {
            $app = basename($dir);
            if ($app === 'RPC') {
                continue;
            }

            $urls[] = $this->baseUrl() . '/index.php?' . $site . '&' . $app;
        }

        return array_values(array_unique($urls));
    }

    private function adminLogin(string $cookieFile): void
    {
        $user = getenv('OSCOMMERCE_ADMIN_USER') ?: 'admin';
        $pass = getenv('OSCOMMERCE_ADMIN_PASS') ?: 'adminpass123';

        $ch = curl_init($this->baseUrl() . '/index.php?Admin&Login&action=process');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(['username' => $user, 'password' => $pass]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR => $cookieFile,
            CURLOPT_COOKIEFILE => $cookieFile,
            CURLOPT_TIMEOUT => 120,
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    private function httpGetCode(string $url, string $cookieFile): int
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_COOKIEJAR => $cookieFile,
            CURLOPT_COOKIEFILE => $cookieFile,
            CURLOPT_USERAGENT => 'osCommerce-App-Matrix/1.0',
        ]);
        curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $code;
    }
}
