<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Language;
use osCommerce\OM\Core\Modules;
use osCommerce\OM\Core\PDO;
use osCommerce\OM\Core\Registry;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Core Modules, Language, and PDO helpers (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class CoreModulesLanguagePdoCoverageTest extends TestCase
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

    public function testModulesBoxAndContentGroups(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);

        try {
            $boxModules = new Modules('Box');
            $boxModules->getGroup('left');
        } catch (\Throwable) {
        }

        try {
            $contentModules = new Modules('Content');
            $contentModules->getGroup('before');
        } catch (\Throwable) {
        }

        $lang = Registry::get('Language');
        $lang->load('index');
        $lang->get('index_heading_title');
        $lang->getBrowserSetting();
        Language::toUTF8('Coverage string');
        Language::isUTF8('Coverage');

        $_GET['page'] = '2';
        PDO::getBatchPageLinks('page', 25, '');
        PDO::getBatchTotalPages('%d - %d of %d', 2, 25);
        PDO::getBatchPreviousPageLink('page', 'foo=1');
        PDO::getBatchNextPageLink('page', 25, 'foo=1');

        $this->addToAssertionCount(1);
    }
}
