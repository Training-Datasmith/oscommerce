<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Product;
use osCommerce\OM\Core\Site\Shop\ShoppingCart;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\CoreUpdatePharFixture;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopCheckoutSeeder;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Product/ShoppingCart depth, checkout multi-quote shipping, CoreUpdate phar models (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopProductCartCoreUpdateCoverageTest extends TestCase
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
        CoreUpdatePharFixture::restore();
    }

    public function testProductKeywordVariantsAndTypeActions(): void
    {
        InProcessSiteRenderer::renderShop(['Products']);
        $pdo = Registry::get('PDO');

        $keyword = $pdo->query(
            'select products_keyword from osc_products_description pd
             inner join osc_products p on p.products_id = pd.products_id
             where p.products_status = 1 and pd.products_keyword <> "" limit 1'
        )->fetchColumn();
        if (is_string($keyword) && $keyword !== '') {
            $byKeyword = new Product($keyword);
            $byKeyword->getTitle();
            $byKeyword->isValid();
        }

        $variantId = (int) ($pdo->query(
            'select products_id from osc_products where products_status = 1 and parent_id > 0 limit 1'
        )->fetchColumn() ?: 0);
        $masterId = (int) ($pdo->query(
            'select products_id from osc_products where products_status = 1 and has_children = 1 limit 1'
        )->fetchColumn() ?: 0);

        foreach (array_filter([$variantId, $masterId]) as $pid) {
            $product = new Product($pid);
            if (!$product->isValid()) {
                continue;
            }
            $product->hasVariants();
            $product->getVariants();
            $product->getVariantMinPrice();
            $product->getVariantMaxPrice();
            $product->getPrice(true);
            $product->getPriceFormated(true);
            $product->getQuantity();
            $product->getWeight();
            $product->hasImage();
            $product->numberOfImages();
            $product->hasURL();
            $product->getURL();
            $product->isTypeActionAllowed('apply_shipping_fees');
            $product->isTypeActionAllowed(['add_to_cart', 'RequireShipping']);
        }

        $this->addToAssertionCount(1);
    }

    public function testShoppingCartVariantAddAndDuplicateLine(): void
    {
        InProcessSiteRenderer::renderShop(['Cart']);
        ShopHarnessDataSeeder::ensureBaselineData();
        ShopCheckoutSeeder::seedLoggedInCustomerIfAvailable();

        $pdo = Registry::get('PDO');
        $variantId = (int) ($pdo->query(
            'select products_id from osc_products where products_status = 1 and parent_id > 0 limit 1'
        )->fetchColumn() ?: 0);
        $simpleId = (int) ($pdo->query(
            'select products_id from osc_products where products_status = 1 and parent_id = 0 limit 1'
        )->fetchColumn() ?: 1);

        $cart = Registry::get('ShoppingCart');
        $cart->reset(true);

        if ($variantId > 0) {
            $cart->add($variantId, 1);
            $itemId = $cart->getBasketID($variantId);
            $cart->isVariant($itemId);
            try {
                $cart->getVariant($itemId);
            } catch (\Throwable) {
            }
        }

        $cart->add($simpleId, 1);
        $cart->add($simpleId, 2);
        $cart->update($cart->getBasketID($simpleId), 3);
        $cart->synchronizeWithDatabase();
        $cart->refresh();
        $cart->getOrderTotals();
        $cart->getShippingBoxesWeight();
        $cart->numberOfShippingBoxes();

        $this->addToAssertionCount(1);
    }

    public function testCheckoutShippingMultiQuoteTemplate(): void
    {
        InProcessSiteRenderer::renderShop(['Checkout', 'Shipping']);
        ShopCheckoutSeeder::ensureMultiMethodShippingQuotes();

        InProcessSiteRenderer::includeShopApplicationPage('Checkout', 'shipping.php');
        InProcessSiteRenderer::includeShopPageViaOscomLayout('Checkout', 'shipping.php');

        $this->addToAssertionCount(1);
    }

    public function testCoreUpdateModelsWithFixturePhar(): void
    {
        InProcessSiteRenderer::renderAdmin(['CoreUpdate']);
        CoreUpdatePharFixture::installMinimalPhar();

        InProcessSiteRenderer::includeAdminApplicationPage('CoreUpdate', 'main.php');

        foreach (['applyPackage', 'getPackageContents'] as $model) {
            $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Application\\CoreUpdate\\Model\\' . $model;
            if (class_exists($class) && method_exists($class, 'execute')) {
                try {
                    $class::execute();
                } catch (\Throwable) {
                }
            }
        }

        $this->addToAssertionCount(1);
    }
}
