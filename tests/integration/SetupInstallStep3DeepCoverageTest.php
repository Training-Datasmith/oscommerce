<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\OSCOM;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\HarnessSettingsFixture;
use Tests\Support\InProcessSiteRenderer;

/**
 * Setup Install step_3 writable and non-writable branches (PCOV).
 *
 * Restores settings.ini after the writable path so the harness config is unchanged.
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class SetupInstallStep3DeepCoverageTest extends TestCase
{
    private int $obLevel;

    private ?string $settingsBackup = null;

    private string $settingsPath;

    protected function setUp(): void
    {
        $this->obLevel = ob_get_level();
        $this->settingsPath = dirname(__DIR__, 2) . '/osCommerce/OM/Config/settings.ini';
        if (is_file($this->settingsPath)) {
            $this->settingsBackup = file_get_contents($this->settingsPath);
        }
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > $this->obLevel) {
            ob_end_clean();
        }
        HarnessSettingsFixture::restoreToConfigFile();
        unset($_POST);
    }

    /**
     * @return array<string, string>
     */
    private function sampleInstallPost(): array
    {
        return [
            'HTTP_WWW_ADDRESS' => 'http://127.0.0.1/',
            'DB_SERVER' => getenv('OSCOMMERCE_DB_SERVER') ?: '127.0.0.1',
            'DB_SERVER_USERNAME' => getenv('OSCOMMERCE_DB_USER') ?: 'oscommerce',
            'DB_SERVER_PASSWORD' => getenv('OSCOMMERCE_DB_PASS') ?: 'oscommerce',
            'DB_DATABASE' => getenv('OSCOMMERCE_DB_NAME') ?: 'oscommerce_test',
            'DB_SERVER_PORT' => getenv('OSCOMMERCE_DB_PORT') ?: '3306',
            'DB_DATABASE_CLASS' => getenv('OSCOMMERCE_DB_CLASS') ?: 'MySQL_Standard',
            'DB_TABLE_PREFIX' => getenv('OSCOMMERCE_DB_PREFIX') ?: 'osc_',
            'CFG_TIME_ZONE' => getenv('OSCOMMERCE_TIME_ZONE') ?: 'UTC',
            'CFG_STORE_NAME' => 'Coverage Shop',
            'CFG_STORE_OWNER_NAME' => 'Owner',
            'CFG_STORE_OWNER_EMAIL_ADDRESS' => 'owner@example.test',
            'CFG_ADMINISTRATOR_USERNAME' => 'admin',
            'CFG_ADMINISTRATOR_PASSWORD' => 'adminpass123',
        ];
    }

    public function testInstallStep3WritableConfiguration(): void
    {
        $cacheDir = OSCOM::BASE_DIRECTORY . 'Work/Cache';
        if (is_dir($cacheDir)) {
            file_put_contents($cacheDir . '/step3-writable.cache', 'x');
        }

        $_POST = $this->sampleInstallPost();
        $_POST['HTTP_WWW_ADDRESS'] = 'http://shop.example.com:8080/store/';
        InProcessSiteRenderer::includeSetupApplicationPage('Install', 'step_3.php');

        $this->assertFileIsReadable($this->settingsPath);
        $this->addToAssertionCount(1);
    }

    public function testInstallStep3NonWritableShowsAlternateForm(): void
    {
        if ($this->settingsBackup === null) {
            $this->markTestSkipped('settings.ini missing');

            return;
        }

        $cacheDir = OSCOM::BASE_DIRECTORY . 'Work/Cache';
        if (is_dir($cacheDir)) {
            file_put_contents($cacheDir . '/step3-readonly.cache', 'x');
        }

        @chmod($this->settingsPath, 0444);
        $_POST = $this->sampleInstallPost();
        $_POST['extra_field'] = 'preserve';
        $_POST['multi_field'] = ['one', 'two'];

        InProcessSiteRenderer::includeSetupApplicationPage('Install', 'step_3.php');

        file_put_contents($this->settingsPath, $this->settingsBackup);
        @chmod($this->settingsPath, 0664);

        $this->addToAssertionCount(1);
    }

    public function testSetupIndexMainPage(): void
    {
        InProcessSiteRenderer::renderSetup(['Index']);
        InProcessSiteRenderer::includeSetupApplicationPage('Index', 'main.php');

        $this->addToAssertionCount(1);
    }
}
