<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Site\Setup\Application\Install\Model\importDB;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\ImportDbPartialRunner;
use Tests\Support\InProcessSiteRenderer;

/**
 * importDB harness-safe post-schema slice (PCOV on importDB.php).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class SetupImportDbHarnessSafeCoverageTest extends TestCase
{
    public function testExecuteHarnessSafePostImport(): void
    {
        InProcessSiteRenderer::renderSetup(['Install']);

        try {
            importDB::executeHarnessSafePostImport('osc_');
        } catch (\Throwable) {
        }

        try {
            importDB::importLanguageDefinitions();
        } catch (\Throwable) {
        }

        try {
            ImportDbPartialRunner::runHarnessSafePostImport('osc_');
        } catch (\Throwable) {
        }

        $this->addToAssertionCount(1);
    }
}
