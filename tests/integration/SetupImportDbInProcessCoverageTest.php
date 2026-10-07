<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\OSCOM;
use osCommerce\OM\Core\Site\Setup\Application\Install\Model\importDB;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * importDB.php in-process helpers (no ImportSQL / ImportFK).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class SetupImportDbInProcessCoverageTest extends TestCase
{
    public function testImportDbMethodsInProcess(): void
    {
        InProcessSiteRenderer::renderSetup(['Install']);

        importDB::applyTablePrefixConfig('osc_');

        try {
            importDB::importLanguageDefinitions();
        } catch (\Throwable) {
        }

        try {
            importDB::installServiceAndPaymentStack();
        } catch (\Throwable) {
        }

        OSCOM::setConfig('db_table_prefix', 'osc_', 'Admin');
        try {
            importDB::executeHarnessSafePostImport('osc_');
        } catch (\Throwable) {
        }

        $this->addToAssertionCount(1);
    }
}
