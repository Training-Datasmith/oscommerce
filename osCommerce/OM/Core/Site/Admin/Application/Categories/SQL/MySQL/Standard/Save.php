<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Categories\SQL\My_Sql\Standard;

use Os_Commerce\OM\Core\Registry;
/**
 * @since v3.0.2
 */
class Save
{
    public static function execute($data)
    {
        $OSCOM_PDO = Registry::get('PDO');
        $OSCOM_Language = Registry::get('Language');
        if (!isset($data['id'])) {
            $data['id'] = null;
        }
        $error = false;
        $OSCOM_PDO->begin_transaction();
        if (is_numeric($data['id'])) {
            $Qcat = $OSCOM_PDO->prepare('update :table_categories set parent_id = :parent_id, last_modified = now() where categories_id = :categories_id');
            $Qcat->bind_int(':categories_id', $data['id']);
        } else {
            $Qcat = $OSCOM_PDO->prepare('insert into :table_categories (parent_id, date_added) values (:parent_id, now())');
        }
        if ($data['parent_id'] > 0) {
            $Qcat->bind_int(':parent_id', $data['parent_id']);
        } else {
            $Qcat->bind_null(':parent_id');
        }
        $Qcat->execute();
        if (!$Qcat->is_error()) {
            $category_id = is_numeric($data['id']) ? $data['id'] : $OSCOM_PDO->last_insert_id();
            foreach ($OSCOM_Language->get_all() as $l) {
                if (is_numeric($data['id'])) {
                    $Qcd = $OSCOM_PDO->prepare('update :table_categories_description set categories_name = :categories_name where categories_id = :categories_id and language_id = :language_id');
                } else {
                    $Qcd = $OSCOM_PDO->prepare('insert into :table_categories_description (categories_id, language_id, categories_name) values (:categories_id, :language_id, :categories_name)');
                }
                $Qcd->bind_int(':categories_id', $category_id);
                $Qcd->bind_int(':language_id', $l['id']);
                $Qcd->bind_value(':categories_name', $data['name'][$l['id']]);
                $Qcd->execute();
                if ($Qcd->is_error()) {
                    $error = true;
                    break;
                }
            }
            if ($error === false) {
                if (isset($data['image'])) {
                    $Qci = $OSCOM_PDO->prepare('update :table_categories set categories_image = :categories_image where categories_id = :categories_id');
                    $Qci->bind_value(':categories_image', $data['image']);
                    $Qci->bind_int(':categories_id', $category_id);
                    $Qci->execute();
                    if ($Qci->is_error()) {
                        $error = true;
                    }
                }
            }
        }
        if ($error === false) {
            $OSCOM_PDO->commit();
            return true;
        }
        $OSCOM_PDO->roll_back();
        return false;
    }
}