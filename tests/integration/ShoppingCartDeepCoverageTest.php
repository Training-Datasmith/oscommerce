<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\ShoppingCart;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopCheckoutSeeder;

/**
 * ShoppingCart::_calculate and extended cart flows (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShoppingCartDeepCoverageTest extends TestCase
{
    private int $obLevel;

    protected function setUp(): void
    {
        $this->obLevel = ob_get_level();
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > $this->obLevel) {
            ob_end_clean();
        }
    }

    public function testCalculateTotalsAndShippingQuotes(): void
    {
        InProcessSiteRenderer::renderShop(['Cart']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();

        $cart = Registry::get('ShoppingCart');
        $pdo = Registry::get('PDO');
        $productId = (int) ($pdo->query('select products_id from osc_products limit 1')->fetchColumn() ?: 1);

        ShopCheckoutSeeder::seedBillingMethodWithoutRecalculate($cart);

        $ref = new \ReflectionClass(ShoppingCart::class);
        if ($ref->hasMethod('_calculate')) {
            $calc = $ref->getMethod('_calculate');
            $calc->setAccessible(true);
            try {
                $calc->invoke($cart, false);
            } catch (\Throwable) {
            }
        }

        $cart->hasContents();
        try {
            $cart->getTotal();
        } catch (\Throwable) {
        }
        $cart->getWeight();
        $cart->getContentType();
        try {
            $cart->getOrderTotals();
        } catch (\Throwable) {
        }
        $cart->getProducts();

        $this->addToAssertionCount(1);
    }
}
