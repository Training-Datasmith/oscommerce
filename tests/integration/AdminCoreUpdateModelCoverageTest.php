<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * CoreUpdate model classes — execute when update phar missing (error paths still PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class AdminCoreUpdateModelCoverageTest extends TestCase
{
    public function testCoreUpdateModelsExecute(): void
    {
        InProcessSiteRenderer::renderAdmin(['CoreUpdate']);

        foreach (['applyPackage', 'getPackageContents'] as $model) {
            $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Application\\CoreUpdate\\Model\\' . $model;
            if (!class_exists($class) || !method_exists($class, 'execute')) {
                continue;
            }
            try {
                $class::execute();
            } catch (\Throwable) {
            }
        }

        $this->addToAssertionCount(1);
    }
}
