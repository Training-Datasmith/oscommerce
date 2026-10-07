<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\Registry;
use osCommerce\OM\Core\Site\Shop\Module\Payment\CoverageConfirmation;
use osCommerce\OM\Core\Site\Shop\ShoppingCart;

final class CheckoutConfirmationPaymentStub
{
    public static function primePaymentModuleWithConfirmation(): void
    {
        $module = new CoverageConfirmation();
        Registry::set('PaymentModule', $module, true);

        $cart = Registry::get('ShoppingCart');
        $cart->setBillingMethod([
            'id' => 'CoverageConfirmation_CoverageConfirmation',
            'title' => 'Coverage Confirmation',
            'module' => 'Coverage Confirmation',
        ], false);
    }
}
