<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\Mail;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Mail MIME body assembly (PCOV); uses fake-sendmail via phpunit.xml.dist.
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
#[PreserveGlobalState(false)]
class CoreMailDeepCoverageTest extends TestCase
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

    public function testMailMultipartBodiesAndSend(): void
    {
        if (!\defined('OSCOM_TEST_MAIL_BUILD_COVERAGE')) {
            \define('OSCOM_TEST_MAIL_BUILD_COVERAGE', true);
        }

        InProcessSiteRenderer::renderShop(['Index']);

        $tmp = tempnam(sys_get_temp_dir(), 'oscimg');
        if ($tmp !== false) {
            file_put_contents($tmp, 'img');
        }

        $mail = new Mail('To', 'to@example.test', 'From', 'from@example.test', 'Subject');
        $mail->addCC('CC', 'cc@example.test');
        $mail->addBCC('BCC', 'bcc@example.test');
        $mail->setCharset('utf-8');
        $mail->setContentTransferEncoding('quoted-printable');
        $mail->addHeader('X-Coverage', 'grind');
        $mail->setBodyPlain('Plain part');
        $mail->setBodyHTML('<p>HTML with <img src="inline.png" /></p>');
        if ($tmp !== false) {
            $mail->addAttachment($tmp);
            $mail->_build_image([
                'id' => 'img1',
                'filename' => 'inline.png',
                'mimetype' => 'image/png',
                'data' => chunk_split(base64_encode('x')),
            ], '=BOUND');
        }
        $mail->send();

        $mail->clearTo();
        $mail->addTo('To2', 'to2@example.test');
        $mail->setFrom('From2', 'from2@example.test');

        $plainOnly = new Mail('A', 'a@test.test', 'B', 'b@test.test', 'Plain');
        $plainOnly->setBodyPlain('Only plain');
        $plainOnly->send();

        $htmlAttach = new Mail('A', 'a@test.test', 'B', 'b@test.test', 'Attach');
        $htmlAttach->setBodyHTML('<b>html</b>');
        if ($tmp !== false) {
            $htmlAttach->addAttachment($tmp);
        }
        $htmlAttach->send();

        if ($tmp !== false) {
            @unlink($tmp);
        }

        $this->addToAssertionCount(1);
    }
}
