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
class ShopInstallSmokeTest extends TestCase
{
    private function baseUrl(): string
    {
        return rtrim(getenv('OSCOMMERCE_BASE_URL') ?: 'http://localhost', '/');
    }

    public function testShopHomepageLoads(): void
    {
        $response = $this->httpGet($this->baseUrl() . '/index.php?Shop');
        $this->assertSame(200, $response['code']);
        $this->assertStringContainsString('Test Shop', $response['body']);
    }

    public function testSampleProductVisibleInCatalog(): void
    {
        $response = $this->httpGet($this->baseUrl() . '/index.php?Shop&Products&All');
        $this->assertSame(200, $response['code']);
        $this->assertMatchesRegularExpression('/Intel|Matrox|Samsung|product/i', $response['body']);
    }

    /**
     * @return array{code: int, body: string}
     */
    private function httpGet(string $url): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_USERAGENT => 'osCommerce-Integration-Test/1.0',
        ]);
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertNotFalse($body);

        return ['code' => $code, 'body' => (string) $body];
    }
}
