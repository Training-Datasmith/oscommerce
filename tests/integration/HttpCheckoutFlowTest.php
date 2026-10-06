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
class HttpCheckoutFlowTest extends TestCase
{
    private function baseUrl(): string
    {
        return rtrim(getenv('OSCOMMERCE_BASE_URL') ?: 'http://localhost', '/');
    }

    public function testAddProductAndViewCart(): void
    {
        $cookie = sys_get_temp_dir() . '/osc_checkout_cookies.txt';
        @unlink($cookie);

        $home = $this->get($this->baseUrl() . '/index.php?Shop&Products&All', $cookie);
        $this->assertSame(200, $home['code']);

        if (preg_match('/products_id=(\d+)/', $home['body'], $m)) {
            $pid = $m[1];
        } else {
            $pid = '1';
        }

        $add = $this->get($this->baseUrl() . '/index.php?Shop&Products&action=add_product&products_id=' . $pid, $cookie);
        $this->assertLessThan(500, $add['code']);

        $cart = $this->get($this->baseUrl() . '/index.php?Shop&Cart', $cookie);
        $this->assertSame(200, $cart['code']);
        $this->assertStringContainsStringIgnoringCase('cart', $cart['body']);
    }

    /**
     * @return array{code: int, body: string}
     */
    private function get(string $url, string $cookieFile): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR => $cookieFile,
            CURLOPT_COOKIEFILE => $cookieFile,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_USERAGENT => 'osCommerce-Checkout-Flow/1.0',
        ]);
        $body = (string) curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['code' => $code, 'body' => $body];
    }
}
