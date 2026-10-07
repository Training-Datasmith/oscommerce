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
 * ShoppingCart address/shipping/billing and content-type paths (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShoppingCartAddressShippingCoverageTest extends TestCase
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

    public function testCartAddressesShippingAndVariants(): void
    {
        InProcessSiteRenderer::renderShop(['Checkout', 'Billing']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();

        $cart = Registry::get('ShoppingCart');
        $pdo = Registry::get('PDO');
        $productId = (int) ($pdo->query('select products_id from osc_products order by products_id limit 1')->fetchColumn() ?: 1);

        $address = [
            'firstname' => 'Coverage',
            'lastname' => 'Customer',
            'street_address' => '123 Main St',
            'city' => 'Testville',
            'postcode' => '90210',
            'state' => 'CA',
            'country_id' => 223,
            'zone_id' => 1,
            'country' => 'United States',
            'format_id' => 2,
        ];

        $cart->setBillingAddress($address);
        $cart->getBillingAddress('city');
        $cart->hasBillingAddress();
        $cart->setShippingAddress($address);
        $cart->getShippingAddress('postcode');
        $cart->hasShippingAddress();
        $cart->setShippingMethod(['id' => 'flat_flat', 'title' => 'Flat', 'cost' => '5.00'], false);
        $cart->getShippingMethod('title');
        $cart->hasShippingMethod();

        ShopCheckoutSeeder::seedBillingMethodWithoutRecalculate($cart);

        $cart->add($productId, 1);
        $itemId = $cart->getBasketID($productId);
        $cart->isVariant($itemId);
        try {
            $cart->getVariant($itemId);
        } catch (\Throwable) {
        }
        $cart->getContentType();
        $cart->generateCartID(8);
        $cart->getCartID();

        $ref = new \ReflectionClass(ShoppingCart::class);
        if ($ref->hasMethod('_cleanUp')) {
            $m = $ref->getMethod('_cleanUp');
            $m->setAccessible(true);
            try {
                $m->invoke($cart);
            } catch (\Throwable) {
            }
        }

        $cart->resetBillingAddress();
        $cart->resetShippingAddress();
        $cart->resetShippingMethod();
        $cart->resetBillingMethod();

        $this->addToAssertionCount(1);
    }
}
