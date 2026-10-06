<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\Site\Shop\ApplicationAbstract;

/**
 * Minimal Shop application for including page/*.php without action redirects.
 */
final class ShopPageApplicationStub extends ApplicationAbstract
{
    public function __construct()
    {
        $this->_page_title = 'Coverage';
        $this->_page_contents = 'main.php';
    }

    protected function initialize(): void
    {
    }
}
