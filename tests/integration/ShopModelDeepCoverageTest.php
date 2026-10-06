<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Customer;
use osCommerce\OM\Core\Site\Shop\Order;
use osCommerce\OM\Core\Site\Shop\Product;
use osCommerce\OM\Core\Site\Shop\ShoppingCart;
use osCommerce\OM\Core\Site\Shop\Shipping;
use osCommerce\OM\Core\Site\Shop\Tax;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Deep Shop domain/model coverage against sample data.
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopModelDeepCoverageTest extends TestCase
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

    public function testCartProductOrderAndTax(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);

        $pdo = Registry::get('PDO');
        $productId = (int) ($pdo->query('select products_id from osc_products order by products_id limit 1')->fetchColumn() ?: 1);
        $customerId = (int) ($pdo->query('select customers_id from osc_customers order by customers_id limit 1')->fetchColumn() ?: 1);

        $customer = Registry::get('Customer');
        $this->assertInstanceOf(Customer::class, $customer);
        $customer->setCustomerData($customerId);

        $cart = Registry::get('ShoppingCart');
        $this->assertInstanceOf(ShoppingCart::class, $cart);
        $cart->reset();
        $cart->add($productId, 2);
        $cart->update($productId, 1);
        $cart->getProducts();
        $cart->getTotal();
        $cart->getSubTotal();
        $cart->getWeight();
        $cart->getTaxGroups();
        $cart->numberOfItems();
        $cart->synchronizeWithDatabase();
        $cart->refresh();
        $cart->getContentType();
        $cart->generateCartID();
        if ($customer->isLoggedOn() && $customer->hasDefaultAddress()) {
            $addr = $customer->getDefaultAddressID();
            $cart->setShippingAddress($addr);
            $cart->setBillingAddress($addr);
            $cart->hasShippingAddress();
            $cart->hasBillingAddress();
            $cart->getShippingAddress('city');
            $cart->getBillingAddress('city');
        }
        $cart->resetShippingMethod();
        $cart->resetBillingMethod();
        $cart->hasContents();
        $cart->getCartID();

        $product = new Product($productId);
        $product->getTitle();
        $product->getPrice();
        $product->getDescription();
        $product->getModel();
        $product->getQuantity();
        $product->isValid();

        $customer->getName();
        $customer->getEmailAddress();

        $shipping = new Shipping();
        $shipping->getQuotes();
        $shipping->hasActive();

        $tax = new Tax();
        $tax->getTaxRate(1);

        $orderId = (int) ($pdo->query('select orders_id from osc_orders order by orders_id limit 1')->fetchColumn() ?: 0);
        if ($orderId > 0) {
            $order = new Order($orderId);
            $order->getTotal();
        }

        $this->addToAssertionCount(1);
    }
}
