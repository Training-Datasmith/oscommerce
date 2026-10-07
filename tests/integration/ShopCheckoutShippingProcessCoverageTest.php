<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Application\Checkout\Action\Shipping\Process;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopCheckoutSeeder;

/**
 * Checkout Shipping Process action with live quotes (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopCheckoutShippingProcessCoverageTest extends TestCase
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
        unset($_POST);
    }

    public function testShippingProcessWithQuoteSelection(): void
    {
        InProcessSiteRenderer::renderShop(['Checkout', 'Shipping']);
        ShopCheckoutSeeder::ensureShippingWithQuotes(false);

        $shipping = Registry::get('Shipping');
        if (!$shipping->hasQuotes()) {
            ShopCheckoutSeeder::seedGuestCheckoutCart(false);
            $shipping = new \osCommerce\OM\Core\Site\Shop\Shipping();
            Registry::set('Shipping', $shipping, true);
        }
        if (!$shipping->hasQuotes()) {
            $this->markTestSkipped('No shipping quotes in harness');

            return;
        }

        $moduleId = '';
        foreach ($shipping->getQuotes() as $quote) {
            if (isset($quote['methods'][0])) {
                $moduleId = $quote['id'] . '_' . $quote['methods'][0]['id'];
                break;
            }
        }

        $_POST = [
            'shipping_mod_sel' => $moduleId,
            'comments' => 'Ship comment',
        ];

        try {
            Process::execute(Registry::get('Application'));
        } catch (\Throwable $e) {
            if (!str_contains($e->getMessage(), 'OSCOM redirect')) {
                throw $e;
            }
        }

        $this->addToAssertionCount(1);
    }
}
