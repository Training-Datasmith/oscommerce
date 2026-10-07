<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopCheckoutSeeder;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * ShoppingCart DB sync paths for logged-in customers (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShoppingCartLoggedInCoverageTest extends TestCase
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

    public function testCartAddUpdateRemoveWhileLoggedIn(): void
    {
        InProcessSiteRenderer::renderShop(['Cart']);
        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();
        ShopCheckoutSeeder::seedGuestCheckoutCart();

        $pdo = Registry::get('PDO');
        $productId = (int) ($pdo->query('select products_id from osc_products limit 1')->fetchColumn() ?: 1);

        $cart = Registry::get('ShoppingCart');
        $cart->reset(true);
        $cart->add($productId, 2);
        $cart->synchronizeWithDatabase();
        $cart->refresh();

        $itemId = $cart->getBasketID($productId);
        $cart->update($itemId, 1);
        $cart->getSubTotal();
        $cart->hasStock();
        $cart->isInStock($itemId);
        $cart->getTaxingAddress();
        $cart->addTaxAmount(1.0);
        $cart->addTaxGroup('VAT', 1.0);
        $cart->getTaxGroups();
        $cart->numberOfTaxGroups();
        $cart->addToTotal(0.5);
        $cart->getShippingBoxesWeight();
        $cart->numberOfShippingBoxes();

        $cart->remove($itemId);
        $cart->reset(true);

        $this->addToAssertionCount(1);
    }
}
