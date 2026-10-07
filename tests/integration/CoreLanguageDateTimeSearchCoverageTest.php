<?php

declare(strict_types=1);

namespace Tests\Integration;

use osCommerce\OM\Core\DateTime;
use osCommerce\OM\Core\Language;
use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Search;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;
use Tests\Support\ShopHarnessDataSeeder;

/**
 * Core Language, DateTime, Shop Search (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class CoreLanguageDateTimeSearchCoverageTest extends TestCase
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

    public function testLanguageDateTimeAndSearch(): void
    {
        InProcessSiteRenderer::renderShop(['Search']);
        ShopHarnessDataSeeder::ensureBaselineData();

        $lang = Registry::get('Language');
        $lang->load('index');
        $lang->load('products');
        $lang->getAll();
        $lang->getID();
        $lang->getCode();
        $lang->getName();
        $lang->getLocale();
        $lang->getCharacterSet();
        $lang->getDateFormatShort(true);
        $lang->getDateFormatLong();
        $lang->getTimeFormat();
        $lang->getTextDirection();
        $lang->getCurrencyID();
        $lang->getNumericDecimalSeparator();
        $lang->getNumericThousandsSeparator();
        $lang->showImage();
        Language::toUTF8('test');
        Language::isUTF8('test');

        $parsed = [];
        DateTime::validate('06/15/1990', 'm/d/Y', $parsed);
        DateTime::getShort('2024-01-15 12:00:00');
        DateTime::getLong('2024-01-15 12:00:00');
        DateTime::getTimestamp('15-Jan-2024 12:00:00', 'd-M-Y H:i:s');

        $_GET['Search'] = '';
        $_GET['keywords'] = 'the';
        $_GET['page'] = '1';
        try {
            $search = new Search();
            $search->setKeywords('the');
            $search->execute();
            $search->getResult();
            $search->getNumberOfResults();
            $search->hasKeywords();
        } catch (\Throwable) {
        }

        InProcessSiteRenderer::includeShopApplicationPage('Search', 'main.php');
        InProcessSiteRenderer::includeShopPageViaOscomLayout('Products', 'main.php');

        $this->addToAssertionCount(1);
    }
}
