<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\SiteRouteDiscovery;

/**
 * In-process Admin (OM3 Controller apps) route rendering for PCOV coverage.
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class InProcessAdminRouteCoverageTest extends TestCase
{
    public function testRenderDiscoveredAdminRoutes(): void
    {
        foreach (SiteRouteDiscovery::adminRoutes() as $parts) {
            InProcessSiteRenderer::renderAdmin($parts);
        }

        $this->addToAssertionCount(1);
    }

    public function testRenderAdminEntityEditors(): void
    {
        InProcessSiteRenderer::renderAdmin(['Customers'], ['customers_id' => '1']);
        InProcessSiteRenderer::renderAdmin(['Categories'], ['cID' => '0']);
        InProcessSiteRenderer::renderAdmin(['Languages'], ['languages_id' => '1']);
        InProcessSiteRenderer::renderAdmin(['ZoneGroups'], ['zone_groups_id' => '1']);

        $this->addToAssertionCount(1);
    }
}
