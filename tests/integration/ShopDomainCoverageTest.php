<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Product;
use osCommerce\OM\Core\Site\Shop\ShoppingCart;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Tests\Support\LampSiteBootstrap;

/**
 * Exercise Shop domain objects against sample data (line coverage).
 *
 * @group integration
 */
#[Group('integration')]
class ShopDomainCoverageTest extends TestCase
{
    private int $obLevel;

    protected function setUp(): void
    {
        $this->obLevel = ob_get_level();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION = [];
        LampSiteBootstrap::boot('Shop', 'Cart');
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > $this->obLevel) {
            ob_end_clean();
        }
    }

    public function testShoppingCartAddAndTotals(): void
    {
        $cart = Registry::get('ShoppingCart');
        $this->assertInstanceOf(ShoppingCart::class, $cart);

        $productId = $this->firstSampleProductId();
        $cart->add($productId, 1);
        $this->assertTrue($cart->hasContents());
        $cart->getProducts();
        $cart->numberOfItems();
        $cart->getTotal();
        $cart->getSubTotal();
        $cart->getWeight();
        $cart->getTaxGroups();
    }

    public function testProductDetails(): void
    {
        $product = new Product($this->firstSampleProductId());
        $this->assertTrue($product->isValid());
        $product->getTitle();
        $product->getPrice();
        $product->getDescription();
        $product->getModel();
    }

    private function firstSampleProductId(): int
    {
        $pdo = Registry::get('PDO');
        $q = $pdo->query('select products_id from osc_products limit 1');
        $q->execute();
        $row = $q->fetch();

        return (int) ($row['products_id'] ?? 1);
    }
}
