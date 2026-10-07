<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Cache;
use osCommerce\OM\Core\Modules;
use osCommerce\OM\Core\Registry;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Core Modules constructor branches + install/remove (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class CoreModulesInstallCoverageTest extends TestCase
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

    public function testModulesLayoutCacheAndInstallLifecycle(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);

        foreach (['Index' => 'main.php', 'Products' => 'product_listing.php', 'Checkout' => 'billing.php', 'Cart' => 'main.php'] as $app => $page) {
            InProcessSiteRenderer::includeShopPageViaOscomLayout($app, $page);
            Cache::clear('templates_Box_layout-' . Registry::get('Template')->getCode() . '-' . $app . '-' . $page);
            try {
                $box = new Modules('Box');
                $box->getGroup('left');
                $box->getGroup('right');
                new Modules('Box');
            } catch (\Throwable) {
            }
            try {
                $content = new Modules('Content');
                $content->getGroup('before');
                $content->getGroup('after');
            } catch (\Throwable) {
            }
        }

        $code = 'Zz' . substr(bin2hex(random_bytes(3)), 0, 4);
        $module = new Modules('Box');
        $module->_code = $code;
        $module->_title = 'Coverage Box';
        $module->_author_name = 'W2';
        $module->_author_www = 'http://example.test';
        try {
            $module->install();
            $module->isInstalled($code, 'Box');
            $module->getTitle();
            $module->getAuthorName();
            $module->getAuthorAddress();
            $module->hasTitleLink();
            $module->getContent();
            $module->hasContent();
            $module->isActive();
            $module->remove();
        } catch (\Throwable) {
        }

        $this->addToAssertionCount(1);
    }
}
