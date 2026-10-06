<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use osCommerce\OM\Core\Mail;
use PHPUnit\Framework\TestCase;

class MailTest extends TestCase
{
    public function testComposeHeadersAndBodies(): void
    {
        $mail = new Mail('To', 'to@example.com', 'From', 'from@example.com', 'Subject');
        $mail->addCC('CC', 'cc@example.com');
        $mail->addBCC('BCC', 'bcc@example.com');
        $mail->setBodyPlain('Plain');
        $mail->setBodyHTML('<p>HTML</p>');
        $mail->setCharset('utf-8');
        $mail->setContentTransferEncoding('8bit');
        $mail->addHeader('X-Test', '1');

        $tmp = tempnam(sys_get_temp_dir(), 'mail');
        file_put_contents($tmp, 'attachment');
        $mail->addAttachment($tmp, false);

        $this->assertSame('Subject', $mail->_subject);
        $this->assertNotEmpty($mail->_body_plain);
        unlink($tmp);
    }
}
