<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * Payload for Admin Languages SQL Import::execute (PCOV).
 */
final class LanguageImportPayload
{
    /**
     * @return array<string, mixed>
     */
    public static function newLanguageImport(): array
    {
        $code = 'zz_' . substr(md5((string) microtime(true)), 0, 4);

        return [
            'name' => 'Coverage Lang ' . $code,
            'code' => $code,
            'locale' => 'en_US',
            'charset' => 'UTF-8',
            'date_format_short' => '%m/%d/%Y',
            'date_format_long' => '%B %d, %Y',
            'time_format' => '%H:%M:%S',
            'text_direction' => 'ltr',
            'currencies_id' => 1,
            'numeric_separator_decimal' => '.',
            'numeric_separator_thousands' => ',',
            'parent_id' => 0,
            'default_language_id' => 1,
            'import_type' => 'replace',
            'definitions' => [
                ['key' => 'STORE_NAME', 'group' => 'configuration', 'value' => 'Coverage Shop'],
                ['key' => 'index_heading_title', 'group' => 'index', 'value' => 'Home'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function updateLanguageImport(int $languageId): array
    {
        $payload = self::newLanguageImport();
        $payload['id'] = $languageId;
        $payload['import_type'] = 'update';

        return $payload;
    }
}
