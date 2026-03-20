<?php

declare (strict_types=1);
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
class Os_C_weight_Classes_admin
{
    public static function get_data($id)
    {
        global $os_c_database, $os_c_language;
        $Qclass = $os_c_database->query('select * from :table_weight_classes where weight_class_id = :weight_class_id and language_id = :language_id');
        $Qclass->bind_table(':table_weight_classes', TABLE_WEIGHT_CLASS);
        $Qclass->bind_int(':weight_class_id', $id);
        $Qclass->bind_int(':language_id', $os_c_language->get_id());
        $Qclass->execute();
        $data = $Qclass->to_array();
        $Qclass->free_result();
        return $data;
    }
    public static function save($id = null, $data, $default = false)
    {
        global $os_c_database, $os_c_language;
        $error = false;
        $os_c_database->start_transaction();
        if (is_numeric($id)) {
            $weight_class_id = $id;
        } else {
            $Qwc = $os_c_database->query('select max(weight_class_id) as weight_class_id from :table_weight_classes');
            $Qwc->bind_table(':table_weight_classes', TABLE_WEIGHT_CLASS);
            $Qwc->execute();
            $weight_class_id = $Qwc->value_int('weight_class_id') + 1;
        }
        foreach ($os_c_language->get_all() as $l) {
            if (is_numeric($id)) {
                $Qwc = $os_c_database->query('update :table_weight_classes set weight_class_key = :weight_class_key, weight_class_title = :weight_class_title where weight_class_id = :weight_class_id and language_id = :language_id');
            } else {
                $Qwc = $os_c_database->query('insert into :table_weight_classes (weight_class_id, language_id, weight_class_key, weight_class_title) values (:weight_class_id, :language_id, :weight_class_key, :weight_class_title)');
            }
            $Qwc->bind_table(':table_weight_classes', TABLE_WEIGHT_CLASS);
            $Qwc->bind_int(':weight_class_id', $weight_class_id);
            $Qwc->bind_int(':language_id', $l['id']);
            $Qwc->bind_value(':weight_class_key', $data['key'][$l['id']]);
            $Qwc->bind_value(':weight_class_title', $data['name'][$l['id']]);
            $Qwc->set_logging($_SESSION['module'], $weight_class_id);
            $Qwc->execute();
            if ($os_c_database->is_error()) {
                $error = true;
                break;
            }
        }
        if ($error === false) {
            if (is_numeric($id)) {
                $Qrules = $os_c_database->query('select weight_class_to_id from :table_weight_classes_rules where weight_class_from_id = :weight_class_from_id and weight_class_to_id != :weight_class_to_id');
                $Qrules->bind_table(':table_weight_classes_rules', TABLE_WEIGHT_CLASS_RULES);
                $Qrules->bind_int(':weight_class_from_id', $weight_class_id);
                $Qrules->bind_int(':weight_class_to_id', $weight_class_id);
                $Qrules->execute();
                while ($Qrules->next()) {
                    $Qrule = $os_c_database->query('update :table_weight_classes_rules set weight_class_rule = :weight_class_rule where weight_class_from_id = :weight_class_from_id and weight_class_to_id = :weight_class_to_id');
                    $Qrule->bind_table(':table_weight_classes_rules', TABLE_WEIGHT_CLASS_RULES);
                    $Qrule->bind_value(':weight_class_rule', $data['rules'][$Qrules->value_int('weight_class_to_id')]);
                    $Qrule->bind_int(':weight_class_from_id', $weight_class_id);
                    $Qrule->bind_int(':weight_class_to_id', $Qrules->value_int('weight_class_to_id'));
                    $Qrule->set_logging($_SESSION['module'], $weight_class_id);
                    $Qrule->execute();
                    if ($os_c_database->is_error()) {
                        $error = true;
                        break;
                    }
                }
            } else {
                $Qclasses = $os_c_database->query('select weight_class_id from :table_weight_classes where weight_class_id != :weight_class_id and language_id = :language_id');
                $Qclasses->bind_table(':table_weight_classes', TABLE_WEIGHT_CLASS);
                $Qclasses->bind_int(':weight_class_id', $weight_class_id);
                $Qclasses->bind_int(':language_id', $os_c_language->get_id());
                $Qclasses->execute();
                while ($Qclasses->next()) {
                    $Qdefault = $os_c_database->query('insert into :table_weight_classes_rules (weight_class_from_id, weight_class_to_id, weight_class_rule) values (:weight_class_from_id, :weight_class_to_id, :weight_class_rule)');
                    $Qdefault->bind_table(':table_weight_classes_rules', TABLE_WEIGHT_CLASS_RULES);
                    $Qdefault->bind_int(':weight_class_from_id', $Qclasses->value_int('weight_class_id'));
                    $Qdefault->bind_int(':weight_class_to_id', $weight_class_id);
                    $Qdefault->bind_value(':weight_class_rule', '1');
                    $Qdefault->set_logging($_SESSION['module'], $weight_class_id);
                    $Qdefault->execute();
                    if ($os_c_database->is_error()) {
                        $error = true;
                        break;
                    }
                    if ($error === false) {
                        $Qnew = $os_c_database->query('insert into :table_weight_classes_rules (weight_class_from_id, weight_class_to_id, weight_class_rule) values (:weight_class_from_id, :weight_class_to_id, :weight_class_rule)');
                        $Qnew->bind_table(':table_weight_classes_rules', TABLE_WEIGHT_CLASS_RULES);
                        $Qnew->bind_int(':weight_class_from_id', $weight_class_id);
                        $Qnew->bind_int(':weight_class_to_id', $Qclasses->value_int('weight_class_id'));
                        $Qnew->bind_value(':weight_class_rule', $data['rules'][$Qclasses->value_int('weight_class_id')]);
                        $Qnew->set_logging($_SESSION['module'], $weight_class_id);
                        $Qnew->execute();
                        if ($os_c_database->is_error()) {
                            $error = true;
                            break;
                        }
                    }
                }
            }
        }
        if ($error === false) {
            if ($default === true) {
                $Qupdate = $os_c_database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
                $Qupdate->bind_table(':table_configuration', TABLE_CONFIGURATION);
                $Qupdate->bind_int(':configuration_value', $weight_class_id);
                $Qupdate->bind_value(':configuration_key', 'SHIPPING_WEIGHT_UNIT');
                $Qupdate->set_logging($_SESSION['module'], $weight_class_id);
                $Qupdate->execute();
                if ($os_c_database->is_error()) {
                    $error = true;
                }
            }
        }
        if ($error === false) {
            $os_c_database->commit_transaction();
            if ($default === true) {
                Os_C_cache::clear('configuration');
            }
            return true;
        }
        $os_c_database->rollback_transaction();
        return false;
    }
    public static function delete($id)
    {
        global $os_c_database;
        $Qclasses = $os_c_database->query('delete from :table_weight_classes where weight_class_id = :weight_class_id');
        $Qclasses->bind_table(':table_weight_classes', TABLE_WEIGHT_CLASS);
        $Qclasses->bind_int(':weight_class_id', $id);
        $Qclasses->set_logging($_SESSION['module'], $id);
        $Qclasses->execute();
        if (!$os_c_database->is_error()) {
            Os_C_cache::clear('weight-classes');
            Os_C_cache::clear('weight-rules');
            return true;
        }
        return false;
    }
}