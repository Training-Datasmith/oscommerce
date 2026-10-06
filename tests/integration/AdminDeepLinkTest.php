<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 * @group apache
 */
#[Group('integration')]
#[Group('apache')]
class AdminDeepLinkTest extends TestCase
{
    private function baseUrl(): string
    {
        return rtrim(getenv('OSCOMMERCE_BASE_URL') ?: 'http://localhost', '/');
    }

    public function testAdminModulePages(): void
    {
        $cookie = sys_get_temp_dir() . '/osc_admin_deep.txt';
        @unlink($cookie);
        $this->login($cookie);

        $urls = [
            $this->baseUrl() . '/index.php?Admin&products',
            $this->baseUrl() . '/index.php?Admin&products&action=new',
            $this->baseUrl() . '/index.php?Admin&products&action=edit&products_id=1',
            $this->baseUrl() . '/index.php?Admin&categories',
            $this->baseUrl() . '/index.php?Admin&customers',
            $this->baseUrl() . '/index.php?Admin&orders',
            $this->baseUrl() . '/index.php?Admin&Configuration',
            $this->baseUrl() . '/index.php?Admin&modules_payment',
            $this->baseUrl() . '/index.php?Admin&backup',
            $this->baseUrl() . '/index.php?Admin&ServerInfo',
        ];

        foreach ($urls as $url) {
            $code = $this->getCode($url, $cookie);
            $this->assertLessThan(500, $code, $url);
        }
    }

    private function login(string $cookie): void
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
            CURLOPT_COOKIEJAR => $cookie,
            CURLOPT_COOKIEFILE => $cookie,
            CURLOPT_TIMEOUT => 120,
        ]);
        curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $code;
    }
}
