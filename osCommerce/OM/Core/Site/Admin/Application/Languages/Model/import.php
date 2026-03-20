<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Languages\Model;

use Os_Commerce\OM\Core\Cache;
use Os_Commerce\OM\Core\Directory_Listing;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Currencies\Currencies;
use Os_Commerce\OM\Core\Site\Admin\Application\Languages\Languages;
use Os_Commerce\OM\Core\Site\Admin\Language;
use Os_Commerce\OM\Core\XML;
class import
{
    public static function execute($data)
    {
        $source = ['language' => XML::to_array(simplexml_load_file(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages/' . $data['code'] . '.xml'))];
        $language = ['name' => $source['language']['data']['title'], 'code' => $source['language']['data']['code'], 'locale' => $source['language']['data']['locale'], 'charset' => $source['language']['data']['character_set'], 'date_format_short' => $source['language']['data']['date_format_short'], 'date_format_long' => $source['language']['data']['date_format_long'], 'time_format' => $source['language']['data']['time_format'], 'text_direction' => $source['language']['data']['text_direction'], 'currency' => $source['language']['data']['default_currency'], 'numeric_separator_decimal' => $source['language']['data']['numerical_decimal_separator'], 'numeric_separator_thousands' => $source['language']['data']['numerical_thousands_separator'], 'parent_language_code' => isset($source['language']['data']['parent_language_code']) ? $source['language']['data']['parent_language_code'] : '', 'parent_id' => 0];
        if (!Currencies::exists($language['currency'])) {
            $language['currency'] = DEFAULT_CURRENCY;
        }
        $language['currencies_id'] = Currencies::get($language['currency'], 'currencies_id');
        if (!empty($language['parent_language_code']) && Languages::exists($language['parent_language_code'])) {
            $language['parent_id'] = Languages::get($language['parent_language_code'], 'languages_id');
        }
        $language['id'] = Languages::get($language['code'], 'languages_id');
        $language['default_language_id'] = Languages::get(DEFAULT_LANGUAGE, 'languages_id');
        $language['import_type'] = $data['type'];
        $definitions = [];
        if (isset($source['language']['definitions']['definition'])) {
            $definitions = $source['language']['definitions']['definition'];
            if (isset($definitions['key']) && isset($definitions['value']) && isset($definitions['group'])) {
                $definitions = [['key' => $definitions['key'], 'value' => $definitions['value'], 'group' => $definitions['group']]];
            }
        }
        unset($source);
        $oscom_directory_listing = new Directory_Listing(OSCOM::BASE_DIRECTORY . 'Core/Site/Shop/Languages/' . $data['code']);
        $oscom_directory_listing->set_recursive(true);
        $oscom_directory_listing->set_include_directories(false);
        $oscom_directory_listing->set_add_directory_to_filename(true);
        $oscom_directory_listing->set_check_extension('xml');
        foreach ($oscom_directory_listing->get_files() as $files) {
            $definitions = array_merge($definitions, Language::extract_definitions($data['code'] . '/' . $files['name']));
        }
        $language['definitions'] = $definitions;
        if (OSCOM::call_db('Admin\Languages\Import', $language)) {
            Cache::clear('languages');
            return true;
        }
        return false;
    }
}