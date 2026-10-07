<?php

declare(strict_types=1);

namespace osCommerce\OM\Core\Site\Shop\Module\Payment;

/**
 * Test-only payment module for checkout confirmation template branches (PCOV).
 */
class CoverageConfirmation extends \osCommerce\OM\Core\Site\Shop\PaymentModuleAbstract
{
    protected function initialize(): void
    {
        $this->_title = 'Coverage Confirmation';
        $this->_method_title = 'Coverage Confirmation';
        $this->_status = true;
        $this->_gateway_url = 'https://coverage.example.test/gateway';
    }

    public function process(): void
    {
    }

    public function preConfirmationCheck()
    {
        return '';
    }

    public function selection()
    {
        return [
            'id' => $this->_code . '_' . $this->_code,
            'module' => $this->_method_title,
            'fields' => [
                ['title' => 'Memo', 'field' => '<input type="text" name="coverage_memo" />'],
            ],
        ];
    }

    public function confirmation()
    {
        return [
            'title' => 'Coverage payment confirmation',
            'fields' => [
                ['title' => 'Memo', 'field' => '<input type="text" name="coverage_memo" />'],
            ],
            'text' => 'Coverage confirmation note text.',
        ];
    }

    public function getProcessButton()
    {
        return '<button type="submit" name="coverage_pay">Pay</button>';
    }
}
