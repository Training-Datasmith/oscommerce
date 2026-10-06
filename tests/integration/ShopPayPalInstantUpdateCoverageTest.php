<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Application\Cart\RPC\PayPal\ExpressCheckoutInstantUpdate;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\PayPalNvpMock;
use Tests\Support\PaymentModuleTestHelper;
use Tests\Support\ShopCheckoutSeeder;

/**
 * PayPal Express Checkout instant-update RPC callback (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopPayPalInstantUpdateCoverageTest extends TestCase
{
    private int $obLevel;

    protected function setUp(): void
    {
        $this->obLevel = ob_get_level();
        PayPalNvpMock::register();
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > $this->obLevel) {
            ob_end_clean();
        }
    }

    public function testExpressCheckoutInstantUpdateCallback(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        PaymentModuleTestHelper::definePayPalExpressCheckout(true);

        $pdo = Registry::get('PDO');
        $productId = (int) ($pdo->query('select products_id from osc_products limit 1')->fetchColumn() ?: 1);

        $_POST = [
            'L_NUMBER0' => (string) $productId,
            'L_QTY0' => '1',
            'SHIPTOCOUNTRY' => 'US',
            'SHIPTOCITY' => 'Testville',
            'SHIPTOZIP' => '90210',
            'SHIPTOSTATE' => 'CA',
        ];

        ob_start();
        try {
            ExpressCheckoutInstantUpdate::execute();
        } catch (\Throwable) {
        }
        $output = ob_get_clean();

        $this->assertStringContainsString('METHOD=CallbackResponse', $output);
    }
}
