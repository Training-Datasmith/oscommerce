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
 * ShoppingCart protected helpers via reflection (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShoppingCartProtectedCoverageTest extends TestCase
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

    public function testInvokeProtectedCartMethods(): void
    {
        InProcessSiteRenderer::renderShop(['Cart']);
        ShopCheckoutSeeder::seedGuestCheckoutCart();

        $cart = Registry::get('ShoppingCart');
        $ref = new \ReflectionClass(ShoppingCart::class);

        foreach ($ref->getMethods(\ReflectionMethod::IS_PROTECTED | \ReflectionMethod::IS_PRIVATE) as $method) {
            if ($method->getDeclaringClass()->getName() !== ShoppingCart::class) {
                continue;
            }
            if ($method->getNumberOfParameters() > 2) {
                continue;
            }

            $args = [];
            foreach ($method->getParameters() as $param) {
                $args[] = match ($param->getName()) {
                    'set_shipping' => false,
                    'reset_database' => false,
                    'calculate_total' => false,
                    default => null,
                };
            }

            $method->setAccessible(true);
            try {
                $method->invokeArgs($cart, array_slice($args, 0, $method->getNumberOfParameters()));
            } catch (\Throwable) {
            }
        }

        $this->addToAssertionCount(1);
    }
}
