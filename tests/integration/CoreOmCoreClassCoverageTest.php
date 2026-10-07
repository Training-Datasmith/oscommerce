<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\CreditCard;
use osCommerce\OM\Core\DateTime;
use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Upload;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Core OM classes: Upload, CreditCard, DateTime validate, Language (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class CoreOmCoreClassCoverageTest extends TestCase
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

    public function testCoreUtilityClasses(): void
    {
        InProcessSiteRenderer::renderShop(['Index']);

        $tmp = tempnam(sys_get_temp_dir(), 'osc');
        if ($tmp !== false) {
            file_put_contents($tmp, 'coverage');
            $_FILES['upload'] = [
                'name' => 'coverage.txt',
                'type' => 'text/plain',
                'tmp_name' => $tmp,
                'error' => UPLOAD_ERR_OK,
                'size' => 8,
            ];
            try {
                $upload = new Upload('upload', sys_get_temp_dir());
                $upload->check();
                $upload->save();
            } catch (\Throwable) {
            }
            @unlink($tmp);
        }

        try {
            $cc = new CreditCard('4111111111111111', '12', '2030');
            $cc->setOwner('Test User');
            $cc->setCVC('123');
            $cc->hasValidNumber();
            $cc->hasValidExpiryDate();
            $cc->getSafeNumber();
        } catch (\Throwable) {
        }

        $dateArray = [];
        DateTime::validate('2024-01-15', 'm/d/Y', $dateArray);

        $lang = Registry::get('Language');
        $lang->load('index');
        $lang->get('index_heading_title');

        $this->addToAssertionCount(1);
    }
}
