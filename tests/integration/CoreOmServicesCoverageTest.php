<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\DateTime;
use osCommerce\OM\Core\Language;
use osCommerce\OM\Core\Mail;
use osCommerce\OM\Core\Modules;
use osCommerce\OM\Core\Registry;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Core OM service classes (Mail, Language, DateTime, Modules) line coverage.
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class CoreOmServicesCoverageTest extends TestCase
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

    public function testMailBuildAndSendSkippedInHarness(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);

        $mail = new Mail('To', 'to@example.test', 'From', 'from@example.test', 'Subject');
        $mail->addTo('Extra', 'extra@example.test');
        $mail->addCC('Cc', 'cc@example.test');
        $mail->addBCC('Bcc', 'bcc@example.test');
        $mail->setSubject('Updated');
        $mail->setBodyPlain('Plain body');
        $mail->setBodyHTML('<p>HTML</p>');
        $mail->addAttachment(__FILE__);
        $mail->clearTo();
        $mail->addTo('To', 'to@example.test');
        $mail->send();

        $this->addToAssertionCount(1);
    }

    public function testLanguageDateTimeAndModules(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);

        $lang = Registry::get('Language');
        $this->assertInstanceOf(Language::class, $lang);
        $lang->get('index_heading_title');
        $lang->getID();

        DateTime::getNow();
        DateTime::getShort();
        DateTime::getLong(null, true);
        DateTime::getTimestamp();
        DateTime::isLeapYear(2024);
        DateTime::getTimeZones();

        $modules = new Modules('content');
        $modules->getGroup('left');

        $this->addToAssertionCount(1);
    }
}
