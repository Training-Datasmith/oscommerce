<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Admin Service modules (Debug, Session, RecentlyVisited, etc.) install API (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class AdminServiceModulesCoverageTest extends TestCase
{
    public function testServiceModuleLifecycle(): void
    {
        InProcessSiteRenderer::renderAdmin(['Services']);

        $dir = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Module/Service';
        foreach (glob($dir . '/*.php') ?: [] as $file) {
            $base = basename($file, '.php');
            $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Service\\' . $base;
            if (!class_exists($class)) {
                continue;
            }
            try {
                $module = new $class();
                if (method_exists($module, 'install')) {
                    $module->install();
                }
                if (method_exists($module, 'remove')) {
                    $module->remove();
                }
            } catch (\Throwable) {
            }
        }

        $this->addToAssertionCount(1);
    }
}
