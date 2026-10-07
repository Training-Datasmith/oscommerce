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
 * Shop oscom.php layout: content modules, message stacks, multi-app pages (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopOscomLayoutDeepCoverageTest extends TestCase
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

    public function testOscomTemplateWithModulesAndHeaderMessages(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);
        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedGuestCheckoutCart();

        Registry::get('MessageStack')->add('header', 'Coverage header notice');

        InProcessSiteRenderer::includeShopModulePages();

        $apps = [
            ['Index', 'main.php'],
            ['Cart', 'main.php'],
            ['Products', 'main.php'],
            ['Products', 'product_listing.php'],
            ['Search', 'main.php'],
            ['Info', 'main.php'],
        ];

        foreach ($apps as [$app, $page]) {
            $_GET['page'] = '1';
            InProcessSiteRenderer::includeShopPageViaOscomLayout($app, $page);
        }

        $this->assertGreaterThan(3, count($apps));
    }
}
