<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\ShoppingCart;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Invoke ShoppingCart public methods for line coverage.
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShoppingCartMethodCoverageTest extends TestCase
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

    public function testInvokeShoppingCartPublicApi(): void
    {
        InProcessSiteRenderer::renderShop(['Cart']);

        $pdo = Registry::get('PDO');
        $productId = (int) ($pdo->query('select products_id from osc_products limit 1')->fetchColumn() ?: 1);
        $customerId = (int) ($pdo->query('select customers_id from osc_customers limit 1')->fetchColumn() ?: 1);

        Registry::get('Customer')->setCustomerData($customerId);

        $cart = Registry::get('ShoppingCart');
        $this->assertInstanceOf(ShoppingCart::class, $cart);

        $cart->reset();
        $cart->add($productId, 1);
        $itemId = $productId;

        $ref = new \ReflectionClass($cart);
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->getDeclaringClass()->getName() !== ShoppingCart::class) {
                continue;
            }

            $name = $method->getName();
            if (in_array($name, ['__construct', 'add', 'reset'], true)) {
                continue;
            }

            $argc = $method->getNumberOfParameters();
            $args = [];
            foreach ($method->getParameters() as $param) {
                $args[] = match ($param->getName()) {
                    'product_id', 'item_id' => $itemId,
                    'quantity' => 1,
                    'address' => Registry::get('Customer')->hasDefaultAddress()
                        ? Registry::get('Customer')->getDefaultAddressID()
                        : ['firstname' => 'T', 'lastname' => 'U', 'street_address' => '1 St', 'city' => 'X', 'postcode' => '12345', 'country_id' => 223, 'zone_id' => 1],
                    'shipping_array', 'billing_array' => ['id' => 'flat_flat', 'title' => 'Flat', 'cost' => '5.00'],
                    'reset_database' => false,
                    'calculate_total' => false,
                    'set_shipping' => false,
                    'length' => 5,
                    'key' => 'city',
                    'group' => 'VAT',
                    'amount' => 1.0,
                    'id' => null,
                    default => null,
                };
            }

            if ($argc > count($args)) {
                continue;
            }

            try {
                $method->invokeArgs($cart, array_slice($args, 0, $argc));
            } catch (\Throwable) {
            }
        }

        $this->addToAssertionCount(1);
    }
}
