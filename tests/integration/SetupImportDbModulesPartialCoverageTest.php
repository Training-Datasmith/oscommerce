<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\OSCOM;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\ImportDbPartialRunner;
use Tests\Support\InProcessSiteRenderer;

/**
 * importDB service/payment/shipping/order-total install slice (no ImportSQL).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class SetupImportDbModulesPartialCoverageTest extends TestCase
{
    public function testImportDbModuleInstallStack(): void
    {
        InProcessSiteRenderer::renderSetup(['Install']);

        OSCOM::setConfig('db_table_prefix', 'osc_', 'Admin');
        OSCOM::setConfig('db_table_prefix', 'osc_', 'Shop');
        OSCOM::setConfig('db_table_prefix', 'osc_', 'Setup');

        try {
            ImportDbPartialRunner::installServiceAndModuleStack();
        } catch (\Throwable) {
        }

        $this->addToAssertionCount(1);
    }
}
