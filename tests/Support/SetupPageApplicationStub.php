<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\Site\Setup\ApplicationAbstract;

/**
 * Minimal Setup application for including Install wizard pages without RPC redirects.
 */
final class SetupPageApplicationStub extends ApplicationAbstract
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
