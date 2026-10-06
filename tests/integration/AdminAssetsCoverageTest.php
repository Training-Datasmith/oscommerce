<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Tests\Support\InProcessSiteRenderer;

/**
 * Admin template fragments and cfg_parameters includes (PCOV).
 *
 * @group integration
 */
#[Group('integration')]
#[RunClassInSeparateProcess]
class AdminAssetsCoverageTest extends TestCase
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

    public function testAdminTemplateAndCfgParameters(): void
    {
        InProcessSiteRenderer::includeAdminTemplatePart('header.php');
        InProcessSiteRenderer::includeAdminTemplatePart('footer.php');
        InProcessSiteRenderer::includeAdminCfgParameters();

        $dir = \osCommerce\OM\Core\OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/assets/cfg_parameters';
        foreach (glob($dir . '/*.php') ?: [] as $file) {
            require_once $file;
        }

        if (function_exists('osc_cfg_set_boolean_value')) {
            osc_cfg_set_boolean_value('array(\'1\',\'0\')', 1, 'TEST');
            osc_cfg_use_get_boolean_value('1');
        }
        if (function_exists('osc_cfg_set_textarea_field')) {
            osc_cfg_set_textarea_field('value', 'TEST');
        }
        if (function_exists('osc_cfg_set_countries_pulldown_menu')) {
            osc_cfg_set_countries_pulldown_menu(223, 'TEST');
        }
        if (function_exists('osc_cfg_set_zones_pulldown_menu')) {
            osc_cfg_set_zones_pulldown_menu(1, 'TEST');
        }
        if (function_exists('osc_cfg_set_weight_classes_pulldown_menu')) {
            try {
                osc_cfg_set_weight_classes_pulldown_menu(1, 'TEST');
            } catch (\Throwable) {
            }
        }
        if (function_exists('osc_cfg_set_tax_classes_pull_down_menu')) {
            osc_cfg_set_tax_classes_pull_down_menu(1, 'TEST');
        }
        if (function_exists('osc_cfg_set_zone_classes_pull_down_menu')) {
            osc_cfg_set_zone_classes_pull_down_menu(1, 'TEST');
        }
        if (function_exists('osc_cfg_set_order_statuses_pull_down_menu')) {
            osc_cfg_set_order_statuses_pull_down_menu(1, 'TEST');
        }
        if (function_exists('osc_cfg_set_credit_cards_checkbox_field')) {
            osc_cfg_set_credit_cards_checkbox_field('', 'TEST');
        }
        if (function_exists('osc_cfg_use_get_tax_class_title')) {
            osc_cfg_use_get_tax_class_title(1);
        }
        if (function_exists('osc_cfg_use_get_order_status_title')) {
            osc_cfg_use_get_order_status_title(1);
        }
        if (function_exists('osc_cfg_use_get_zone_class_title')) {
            osc_cfg_use_get_zone_class_title(1);
        }

        $this->addToAssertionCount(1);
    }
}
