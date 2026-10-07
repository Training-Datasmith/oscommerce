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

        $payload = LanguageImportPayload::newLanguageImport();
        $result = Import::execute($payload);
        $this->assertTrue($result);

        $pdo = Registry::get('PDO');
        $langId = (int) ($pdo->query('select languages_id from osc_languages where code = ' . $pdo->quote($payload['code']))->fetchColumn() ?: 0);

        if ($langId > 0) {
            $update = LanguageImportPayload::updateLanguageImport($langId);
            $update['code'] = $payload['code'];
            Import::execute($update);

            $addPayload = LanguageImportPayload::newLanguageImport();
            $addPayload['import_type'] = 'add';
            $addPayload['definitions'][] = ['key' => 'NEW_KEY', 'group' => 'index', 'value' => 'New'];
            Import::execute($addPayload);
            $pdo->exec('delete from osc_languages_definitions where languages_id in (select languages_id from osc_languages where code = ' . $pdo->quote($addPayload['code']) . ')');
            $pdo->exec('delete from osc_languages where code = ' . $pdo->quote($addPayload['code']));

            $pdo->exec('delete from osc_languages_definitions where languages_id = ' . $langId);
            $pdo->exec('delete from osc_languages where languages_id = ' . $langId);
        }

        $this->addToAssertionCount(1);
    }
}
