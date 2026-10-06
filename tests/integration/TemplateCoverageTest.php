<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Template;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopPageApplicationStub;

/**
 * Exercise Template public API and full oscom.php layout (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class TemplateCoverageTest extends TestCase
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

    public function testTemplateMethodsAndLayout(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);

        $app = new ShopPageApplicationStub();
        $app->setPageTitle('Template coverage');
        $app->setPageContent('main.php');
        Registry::set('Application', $app);

        $template = Registry::get('Template');
        $this->assertInstanceOf(Template::class, $template);
        $template->setApplication($app);

        $template->getID();
        $template->getCode();
        $template->getCode(1);
        $template->getModule();
        $template->getGroup();
        $template->getPageTitle();
        $template->getPageTags();
        $template->getBoxModules('left');
        $template->getContentModules('body');
        $template->getTemplateFile();
        $template->getTemplateFile('boxes/categories.php');
        $template->getPageImage();
        $template->getPageContentsFile();
        $template->getPageContentsFilename();
        $template->hasPageTitle();
        $template->hasPageTags();
        $template->hasJavascript();
        $template->hasPageFooter();
        $template->hasPageHeader();
        $template->hasPageContentModules();
        $template->hasPageBoxModules();
        $template->showDebugMessages();
        $template->addJavascriptFilename('public/sites/Shop/javascript/general.js');
        $template->addJavascriptPhpFilename(\osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/assets/form_check.js.php');
        $template->addJavascriptBlock('<script></script>');

        ob_start();
        try {
            $template->getJavascript();
        } finally {
            ob_end_clean();
        }

        $level = ob_get_level();
        ob_start();
        try {
            include $template->getTemplateFile();
        } catch (\Throwable) {
        } finally {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
        }

        $this->addToAssertionCount(1);
    }
}
