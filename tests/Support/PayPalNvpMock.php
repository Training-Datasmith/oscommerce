<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * NVP-style responses for PayPal Express Checkout HTTP calls in tests.
 */
final class PayPalNvpMock
{
    /** @var array<string, string> NVP METHOD => ACK (Success|Failure|SuccessWithWarning) */
    private static array $methodAck = [];

    private static string $lastUrl = '';

    private static string $lastBody = '';

    public static function register(): void
    {
        if (!\defined('OSCOM_TEST_HTTP_MOCK')) {
            \define('OSCOM_TEST_HTTP_MOCK', [self::class, 'respond']);
        }
    }

    public static function setMethodAck(string $method, string $ack): void
    {
        self::$methodAck[$method] = $ack;
    }

    public static function clearOverrides(): void
    {
        self::$methodAck = [];
    }

    public static function lastUrl(): string
    {
        return self::$lastUrl;
    }

    public static function lastBody(): string
    {
        return self::$lastBody;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public static function respond(array $parameters): string
    {
        self::$lastUrl = (string) ($parameters['url'] ?? '');
        $body = (string) ($parameters['parameters'] ?? '');
        self::$lastBody = $body;
        $method = 'SetExpressCheckout';

        if (preg_match('/(?:^|&)METHOD=([^&]+)/', $body, $matches)) {
            $method = urldecode($matches[1]);
        }

        $ack = self::$methodAck[$method] ?? 'Success';

        if ($ack === 'Failure') {
            return 'ACK=Failure&L_LONGMESSAGE0=Mock+' . urlencode($method) . '+failure';
        }

        if ($ack === 'SuccessWithWarning') {
            return match ($method) {
                'SetExpressCheckout' => 'ACK=SuccessWithWarning&TOKEN=EC-TEST-TOKEN',
                'GetExpressCheckoutDetails' => 'ACK=SuccessWithWarning&TOKEN=EC-TEST-TOKEN&PAYERID=TEST-PAYER&PAYERSTATUS=verified&ADDRESSSTATUS=Confirmed',
                'DoExpressCheckoutPayment' => 'ACK=SuccessWithWarning&PAYMENTINFO_0_TRANSACTIONID=TEST-TX',
                default => 'ACK=SuccessWithWarning',
            };
        }

        return match ($method) {
            'SetExpressCheckout' => 'ACK=Success&TOKEN=EC-TEST-TOKEN',
            'GetExpressCheckoutDetails' => 'ACK=Success&TOKEN=EC-TEST-TOKEN&PAYERID=TEST-PAYER&PAYERSTATUS=verified&ADDRESSSTATUS=Confirmed',
            'DoExpressCheckoutPayment' => 'ACK=Success&PAYMENTINFO_0_TRANSACTIONID=TEST-TX',
            default => 'ACK=Failure&L_LONGMESSAGE0=Mock+unknown+method',
        };
    }
}
