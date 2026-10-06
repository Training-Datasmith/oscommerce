<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * HTTP crawl of Shop + Admin (post-login) for integration line coverage.
 *
 * @group integration
 * @group apache
 */
#[Group('integration')]
#[Group('apache')]
class HttpSiteCrawlTest extends TestCase
{
    private function baseUrl(): string
    {
        return rtrim(getenv('OSCOMMERCE_BASE_URL') ?: 'http://localhost', '/');
    }

    public function testCrawlShopAndAdminPages(): void
    {
        $visited = [];
        $queue = [
            $this->baseUrl() . '/index.php?Shop',
            $this->baseUrl() . '/index.php?Shop&Products&All',
            $this->baseUrl() . '/index.php?Shop&Cart',
            $this->baseUrl() . '/index.php?Shop&Account',
            $this->baseUrl() . '/index.php?Shop&Contact',
            $this->baseUrl() . '/index.php?Admin&Login',
        ];

        $cookieFile = sys_get_temp_dir() . '/oscommerce_crawl_cookies.txt';
        @unlink($cookieFile);

        $max = 80;
        while ($queue && $max-- > 0) {
            $url = array_shift($queue);
            if (isset($visited[$url])) {
                continue;
            }
            $visited[$url] = true;

            $response = $this->httpGet($url, $cookieFile);
            $this->assertLessThan(500, $response['code'], $url);

            if (str_contains($url, 'Admin&Login') && $response['code'] === 200) {
                $this->adminLogin($cookieFile);
                $queue[] = $this->baseUrl() . '/index.php?Admin';
            }

            foreach ($this->extractInternalLinks($response['body']) as $link) {
                if (!isset($visited[$link]) && (str_contains($link, '?Shop') || str_contains($link, '?Admin'))) {
                    $queue[] = $link;
                }
            }
        }

        $this->assertGreaterThan(5, count($visited));
    }

    private function adminLogin(string $cookieFile): void
    {
        $user = getenv('OSCOMMERCE_ADMIN_USER') ?: 'admin';
        $pass = getenv('OSCOMMERCE_ADMIN_PASS') ?: 'adminpass123';

        $ch = curl_init($this->baseUrl() . '/index.php?Admin&Login&action=process');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'username' => $user,
                'password' => $pass,
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR => $cookieFile,
            CURLOPT_COOKIEFILE => $cookieFile,
            CURLOPT_TIMEOUT => 120,
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * @return array{code: int, body: string}
     */
    private function httpGet(string $url, string $cookieFile): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_COOKIEJAR => $cookieFile,
            CURLOPT_COOKIEFILE => $cookieFile,
            CURLOPT_USERAGENT => 'osCommerce-Coverage-Crawl/1.0',
        ]);
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['code' => $code, 'body' => (string) $body];
    }

    /**
     * @return list<string>
     */
    private function extractInternalLinks(string $html): array
    {
        $links = [];
        if (preg_match_all('/href=["\']([^"\']+index\.php\?[^"\']+)["\']/i', $html, $m)) {
            foreach ($m[1] as $href) {
                if (str_starts_with($href, 'http')) {
                    $links[] = $href;
                } elseif (str_starts_with($href, '/')) {
                    $links[] = $this->baseUrl() . $href;
                } else {
                    $links[] = $this->baseUrl() . '/' . ltrim($href, '/');
                }
            }
        }

        return array_values(array_unique($links));
    }
}
