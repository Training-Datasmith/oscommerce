<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * NVP-style responses for PayPal Express Checkout HTTP calls in tests.
 */
final class PayPalNvpMock
{
    public static function register(): void
    {
        if (!\defined('OSCOM_TEST_HTTP_MOCK')) {
            \define('OSCOM_TEST_HTTP_MOCK', [self::class, 'respond']);
        }
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public static function respond(array $parameters): string
    {
        $body = (string) ($parameters['parameters'] ?? '');
        $method = 'SetExpressCheckout';

        if (preg_match('/(?:^|&)METHOD=([^&]+)/', $body, $matches)) {
            $method = urldecode($matches[1]);
        }

        return match ($method) {
            'SetExpressCheckout' => 'ACK=Success&TOKEN=EC-TEST-TOKEN',
            'GetExpressCheckoutDetails' => 'ACK=Success&TOKEN=EC-TEST-TOKEN&PAYERID=TEST-PAYER&PAYERSTATUS=verified&ADDRESSSTATUS=Confirmed',
            'DoExpressCheckoutPayment' => 'ACK=Success&PAYMENTINFO_0_TRANSACTIONID=TEST-TX',
            default => 'ACK=Failure&L_LONGMESSAGE0=Mock+unknown+method',
        };
    }
}
