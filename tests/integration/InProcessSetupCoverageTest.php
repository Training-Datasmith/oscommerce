<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\SiteRouteDiscovery;

/**
 * Setup wizard pages (read-only UI) for PCOV coverage.
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class InProcessSetupCoverageTest extends TestCase
{
    public function testRenderSetupPages(): void
    {
        foreach (SiteRouteDiscovery::setupRoutes() as $parts) {
            InProcessSiteRenderer::renderSetup($parts);
        }

        foreach (SiteRouteDiscovery::setupInstallStepParams() as $params) {
            InProcessSiteRenderer::renderSetup(['Install'], $params);
        }

        $this->addToAssertionCount(1);
    }
}
