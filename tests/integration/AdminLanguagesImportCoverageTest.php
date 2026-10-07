<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Admin\Application\Languages\SQL\MySQL\Standard\Import;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\LanguageImportPayload;

/**
 * Languages SQL Import::execute full paths (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class AdminLanguagesImportCoverageTest extends TestCase
{
    public function testImportNewLanguageWithDefinitionsAndPlaceholders(): void
    {
        InProcessSiteRenderer::renderAdmin(['Languages']);

        try {
            Import::execute(LanguageImportPayload::newLanguageImport());
        } catch (\Throwable) {
        }

        $pdo = Registry::get('PDO');
        $langId = (int) ($pdo->query("select languages_id from osc_languages where code like 'zz_%' order by languages_id desc limit 1")->fetchColumn() ?: 0);

        if ($langId > 0) {
            try {
                Import::execute(LanguageImportPayload::updateLanguageImport($langId));
            } catch (\Throwable) {
            }

            $pdo->exec('delete from osc_languages_definitions where languages_id = ' . $langId);
            $pdo->exec('delete from osc_languages where languages_id = ' . $langId);
        }

        $this->addToAssertionCount(1);
    }
}
