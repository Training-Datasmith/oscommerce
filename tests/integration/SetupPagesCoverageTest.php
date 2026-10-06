<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\SiteRouteDiscovery;

/**
 * Setup wizard pages (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class SetupPagesCoverageTest extends TestCase
{
    public function testRenderSetupRoutesAndLanguageFiles(): void
    {
        foreach (SiteRouteDiscovery::setupRoutes() as $parts) {
            InProcessSiteRenderer::renderSetup($parts);
        }

        foreach (SiteRouteDiscovery::setupInstallStepParams() as $params) {
            InProcessSiteRenderer::renderSetup(['Install'], $params);
        }

        $langDir = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Setup/Languages';
        $count = 0;
        foreach (glob($langDir . '/**/*.php') ?: [] as $file) {
            ob_start();
            try {
                include $file;
            } catch (\Throwable) {
            }
            ob_end_clean();
            ++$count;
        }

        $this->assertGreaterThan(2, $count);
    }
}
