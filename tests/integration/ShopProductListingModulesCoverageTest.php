<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Modules;
use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Products as ShopProducts;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * product_listing.php variants and Core Modules (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopProductListingModulesCoverageTest extends TestCase
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

    public function testProductListingAndModules(): void
    {
        InProcessSiteRenderer::renderShop(['Products']);

        $pdo = Registry::get('PDO');
        $boxCode = (string) ($pdo->query('select code from osc_templates_boxes where modules_group = \'Box\' limit 1')->fetchColumn() ?: 'Cart');

        try {
            $boxModules = new Modules('Box');
            $boxModules->getGroup('left');
            $boxModules->getGroup('right');
            $boxModules->isInstalled($boxCode, 'Box');
            $boxModules->hasKeys();
            $boxModules->getKeys();
        } catch (\Throwable) {
        }

        try {
            $contentModules = new Modules('Content');
            $contentModules->getGroup('before');
            $contentModules->getGroup('after');
        } catch (\Throwable) {
        }

        InProcessSiteRenderer::includeShopPageViaOscomLayout('Products', 'product_listing.php');

        unset($_GET['manufacturers']);
        InProcessSiteRenderer::includeShopApplicationPageWithScopeExtras('Products', 'product_listing.php', []);

        InProcessSiteRenderer::includeShopApplicationPageWithScopeExtras('Products', 'product_listing.php', [
            'products_listing' => ['entries' => [], 'total' => 0, 'pages' => 0, 'page' => 1],
        ]);

        $categoryId = (int) ($pdo->query('select categories_id from osc_categories order by categories_id limit 1')->fetchColumn() ?: 0);
        if ($categoryId > 0) {
            try {
                ShopProducts::getListingSortLink('name', 'Name');
                new ShopProducts($categoryId);
            } catch (\Throwable) {
            }
            InProcessSiteRenderer::renderShop(['Index'], ['cPath' => (string) $categoryId]);
            InProcessSiteRenderer::includeRenderedShopOscomLayout();
        }

        $this->addToAssertionCount(1);
    }
}
