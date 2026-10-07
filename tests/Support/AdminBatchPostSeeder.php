<?php

declare(strict_types=1);

namespace Tests\Support;

use osCommerce\OM\Core\Registry;

/**
 * Seed $_POST['batch'] (and related GET) for Admin batch_* confirmation pages (PCOV).
 */
final class AdminBatchPostSeeder
{
    public static function seedForPage(string $application, string $pageFilename): void
    {
        if (!str_contains($pageFilename, 'batch_')) {
            return;
        }

        $pdo = Registry::get('PDO');
        $ids = [];

        switch ($application) {
            case 'Countries':
                if (str_contains($pageFilename, 'zones_')) {
                    $ids = $pdo->query('select zone_id from osc_zones order by zone_id limit 2')->fetchAll(\PDO::FETCH_COLUMN);
                } else {
                    $ids = $pdo->query('select countries_id from osc_countries order by countries_id limit 2')->fetchAll(\PDO::FETCH_COLUMN);
                }
                break;
            case 'TaxClasses':
                if (str_contains($pageFilename, 'entries_')) {
                    $taxClassId = (int) ($pdo->query('select tax_class_id from osc_tax_class order by tax_class_id limit 1')->fetchColumn() ?: 0);
                    if ($taxClassId > 0) {
                        $_GET['id'] = (string) $taxClassId;
                    }
                    $ids = $pdo->query('select tax_rates_id from osc_tax_rates order by tax_rates_id limit 2')->fetchAll(\PDO::FETCH_COLUMN);
                } else {
                    $ids = $pdo->query('select tax_class_id from osc_tax_class order by tax_class_id limit 2')->fetchAll(\PDO::FETCH_COLUMN);
                }
                break;
            case 'ZoneGroups':
                if (str_contains($pageFilename, 'entries_')) {
                    $geoId = (int) ($pdo->query('select geo_zone_id from osc_geo_zones order by geo_zone_id limit 1')->fetchColumn() ?: 0);
                    if ($geoId > 0) {
                        $_GET['id'] = (string) $geoId;
                    }
                    $ids = $pdo->query('select zone_id from osc_zones order by zone_id limit 2')->fetchAll(\PDO::FETCH_COLUMN);
                } else {
                    $ids = $pdo->query('select geo_zone_id from osc_geo_zones order by geo_zone_id limit 2')->fetchAll(\PDO::FETCH_COLUMN);
                }
                break;
            case 'Administrators':
                $ids = $pdo->query('select id from osc_administrators order by id limit 2')->fetchAll(\PDO::FETCH_COLUMN);
                break;
            case 'Currencies':
                $ids = $pdo->query('select currencies_id from osc_currencies order by currencies_id limit 2')->fetchAll(\PDO::FETCH_COLUMN);
                break;
            case 'Languages':
                $ids = $pdo->query('select languages_id from osc_languages order by languages_id limit 2')->fetchAll(\PDO::FETCH_COLUMN);
                break;
            case 'CreditCards':
                $ids = $pdo->query('select id from osc_credit_cards order by id limit 2')->fetchAll(\PDO::FETCH_COLUMN);
                break;
            case 'Configuration':
                $groupId = (int) ($pdo->query('select configuration_group_id from osc_configuration group by configuration_group_id order by configuration_group_id limit 1')->fetchColumn() ?: 0);
                if ($groupId > 0) {
                    $_GET['id'] = (string) $groupId;
                }
                $ids = $pdo->query(
                    'select configuration_id from osc_configuration where configuration_group_id = ' . max($groupId, 1) . ' order by configuration_id limit 3'
                )->fetchAll(\PDO::FETCH_COLUMN);
                break;
            case 'Categories':
                $ids = $pdo->query('select categories_id from osc_categories order by categories_id limit 2')->fetchAll(\PDO::FETCH_COLUMN);
                break;
            default:
                break;
        }

        $ids = array_values(array_filter(array_map('intval', $ids), static fn (int $id): bool => $id > 0));
        if ($ids !== []) {
            $_POST['batch'] = $ids;
        }
    }
}
