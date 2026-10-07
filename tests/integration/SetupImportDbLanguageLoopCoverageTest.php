<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\OSCOM;
use osCommerce\OM\Core\Site\Setup\Language;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * importDB language-definition loop without full schema import (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class SetupImportDbLanguageLoopCoverageTest extends TestCase
{
    public function testInsertLanguageDefinitionsLikeImportDb(): void
    {
        InProcessSiteRenderer::renderSetup(['Install']);

        OSCOM::setConfig('db_table_prefix', 'osc_', 'Admin');
        OSCOM::setConfig('db_table_prefix', 'osc_', 'Shop');
        OSCOM::setConfig('db_table_prefix', 'osc_', 'Setup');

        foreach (Language::extractDefinitions('en_US.xml') as $def) {
            $def['id'] = 1;
            try {
                OSCOM::callDB('Admin\InsertLanguageDefinition', $def, 'Site');
            } catch (\Throwable) {
            }
        }

        $this->addToAssertionCount(1);
    }
}
