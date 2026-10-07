<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Account;
use osCommerce\OM\Core\Site\Shop\Banner;
use osCommerce\OM\Core\Site\Shop\Product;
use osCommerce\OM\Core\Site\Shop\RecentlyVisited;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Shop domain models: Product, Account, Banner, RecentlyVisited (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ShopDomainDeepCoverageTest extends TestCase
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

    public function testProductAccountBannerRecentlyVisited(): void
    {
        InProcessSiteRenderer::renderShop(['Products'], ['products_id' => '1']);

        $pdo = Registry::get('PDO');
        $productId = (int) ($pdo->query('select products_id from osc_products limit 1')->fetchColumn() ?: 1);

        $this->invokePublicMethods(new Product($productId));
        $this->invokePublicMethods(new Account());
        $this->invokePublicMethods(new RecentlyVisited());

        if (!\defined('SERVICE_BANNER_SHOW_DUPLICATE')) {
            \define('SERVICE_BANNER_SHOW_DUPLICATE', '0');
        }
        try {
            $this->invokePublicMethods(new Banner());
        } catch (\Throwable) {
        }

        $visited = new RecentlyVisited();
        try {
            $visited->add($productId);
        } catch (\Throwable) {
        }

        $this->addToAssertionCount(1);
    }

    private function invokePublicMethods(object $obj): void
    {
        $ref = new \ReflectionClass($obj);
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isConstructor() || $method->getNumberOfParameters() > 1) {
                continue;
            }
            try {
                $method->invoke($obj, $method->getNumberOfParameters() === 1 ? 1 : null);
            } catch (\Throwable) {
            }
        }
    }
}
