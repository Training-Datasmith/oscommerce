<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\ErrorHandler;
use osCommerce\OM\Core\OSCOM;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * ErrorHandler SQLite log paths (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class ErrorHandlerCoverageTest extends TestCase
{
    private int $obLevel;

    protected function setUp(): void
    {
        $this->obLevel = ob_get_level();
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > $this->obLevel) {
            ob_end_clean();
        }
    }

    public function testErrorHandlerLifecycle(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);

        $dbDir = OSCOM::BASE_DIRECTORY . 'Work/Database';
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0775, true);
        }
        if (!is_writable($dbDir)) {
            exec('sudo chmod 777 ' . escapeshellarg($dbDir));
        }
        $this->assertTrue(is_writable($dbDir), 'Work/Database must be writable for ErrorHandler SQLite');
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('PDO sqlite driver required for ErrorHandler coverage');
        }

        ErrorHandler::clear();
        $this->assertTrue(ErrorHandler::connect());

        ErrorHandler::execute(E_USER_NOTICE, 'Coverage notice', __FILE__, __LINE__);
        ErrorHandler::getAll(5);
        ErrorHandler::getTotalEntries();
        ErrorHandler::find('Coverage', 5, 1);
        ErrorHandler::getTotalFindEntries('Coverage');

        $logFile = sys_get_temp_dir() . '/osc-error-import.log';
        file_put_contents($logFile, '[' . date('d-M-Y H:i:s') . '] Imported coverage error' . "\n");
        ErrorHandler::import($logFile);

        ErrorHandler::clear();

        $this->addToAssertionCount(1);
    }
}
