<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\PDO\My_Sql\Standard;

use Os_Commerce\OM\Core\OSCOM;
class PDOStatement extends \Os_Commerce\OM\Core\PDOStatement
{
    protected $_pdo;
    protected function __construct($pdo)
    {
        $this->_pdo = $pdo;
    }
    public function execute($input_parameters = [])
    {
        $query_action = strtolower(substr($this->query_string, 0, strpos($this->query_string, ' ')));
        $db_table_prefix = OSCOM::get_config('db_table_prefix');
        if ($query_action == 'delete') {
            $query_data = explode(' ', $this->query_string, 4);
            $query_table = substr($query_data[2], strlen($db_table_prefix));
            if ($this->_pdo->has_foreign_key($query_table)) {
                // check for RESTRICT constraints first
                foreach ($this->_pdo->get_foreign_keys($query_table) as $fk) {
                    if ($fk['on_delete'] == 'restrict') {
                        $Qchild = $this->_pdo->prepare('select ' . $fk['to_field'] . ' from ' . $query_data[2] . ' ' . $query_data[3]);
                        foreach ($this->_binded_params as $key => $value) {
                            $Qchild->bind_value($key, $value['value'], $value['data_type']);
                        }
                        $Qchild->execute();
                        while ($Qchild->fetch()) {
                            $Qcheck = $this->_pdo->prepare('select ' . $fk['from_field'] . ' from ' . $db_table_prefix . $fk['from_table'] . ' where ' . $fk['from_field'] . ' = "' . $Qchild->value($fk['to_field']) . '" limit 1');
                            $Qcheck->execute();
                            if (count($Qcheck->fetch_all()) === 1) {
                                trigger_error('RESTRICT constraint condition from table ' . $db_table_prefix . $fk['from_table']);
                                return false;
                            }
                        }
                    }
                }
                foreach ($this->_pdo->get_foreign_keys($query_table) as $fk) {
                    $Qparent = $this->_pdo->prepare('select * from ' . $query_data[2] . ' ' . $query_data[3]);
                    foreach ($this->_binded_params as $key => $value) {
                        $Qparent->bind_value($key, $value['value'], $value['data_type']);
                    }
                    $Qparent->execute();
                    while ($Qparent->fetch()) {
                        if ($fk['on_delete'] == 'cascade') {
                            $Qdel = $this->_pdo->prepare('delete from ' . $db_table_prefix . $fk['from_table'] . ' where ' . $fk['from_field'] . ' = :' . $fk['from_field']);
                            $Qdel->bind_value(':' . $fk['from_field'], $Qparent->value($fk['to_field']));
                            $Qdel->execute();
                        } elseif ($fk['on_delete'] == 'set_null') {
                            $Qupdate = $this->_pdo->prepare('update ' . $db_table_prefix . $fk['from_table'] . ' set ' . $fk['from_field'] . ' = null where ' . $fk['from_field'] . ' = :' . $fk['from_field']);
                            $Qupdate->bind_value(':' . $fk['from_field'], $Qparent->value($fk['to_field']));
                            $Qupdate->execute();
                        }
                    }
                }
            }
        } elseif ($query_action == 'update') {
            $query_data = explode(' ', $this->query_string, 3);
            $query_table = substr($query_data[1], strlen($db_table_prefix));
            if ($this->_pdo->has_foreign_key($query_table)) {
                // check for RESTRICT constraints first
                foreach ($this->_pdo->get_foreign_keys($query_table) as $fk) {
                    if ($fk['on_update'] == 'restrict') {
                        $Qchild = $this->_pdo->prepare('select ' . $fk['to_field'] . ' from ' . $query_data[2] . ' ' . $query_data[3]);
                        foreach ($this->_binded_params as $key => $value) {
                            $Qchild->bind_value($key, $value['value'], $value['data_type']);
                        }
                        $Qchild->execute();
                        while ($Qchild->fetch()) {
                            $Qcheck = $this->_pdo->prepare('select ' . $fk['from_field'] . ' from ' . $db_table_prefix . $fk['from_table'] . ' where ' . $fk['from_field'] . ' = "' . $Qchild->value($fk['to_field']) . '" limit 1');
                            $Qcheck->execute();
                            if (count($Qcheck->fetch_all()) === 1) {
                                trigger_error('RESTRICT constraint condition from table ' . $db_table_prefix . $fk['from_table']);
                                return false;
                            }
                        }
                    }
                }
                foreach ($this->_pdo->get_foreign_keys($query_table) as $fk) {
                    // check to see if foreign key column value is being changed
                    if (strpos(substr($this->query_string, strpos($this->query_string, ' set ') + 4, strpos($this->query_string, ' where ') - strpos($this->query_string, ' set ') - 4), ' ' . $fk['to_field'] . ' ') !== false) {
                        $Qparent = $this->_pdo->prepare('select * from ' . $query_data[1] . substr($this->query_string, strrpos($this->query_string, ' where ')));
                        foreach ($this->_binded_params as $key => $value) {
                            if (preg_match('/:\b' . substr($key, 1) . '\b/', $Qparent->query_string)) {
                                $Qparent->bind_value($key, $value['value'], $value['data_type']);
                            }
                        }
                        $Qparent->execute();
                        while ($Qparent->fetch()) {
                            if ($fk['on_update'] == 'cascade' || $fk['on_update'] == 'set_null') {
                                $on_update_value = '';
                                if ($fk['on_update'] == 'cascade') {
                                    $on_update_value = $this->_binded_params[':' . $fk['to_field']]['value'];
                                }
                                $Qupdate = $this->_pdo->prepare('update ' . $db_table_prefix . $fk['from_table'] . ' set ' . $fk['from_field'] . ' = :' . $fk['from_field'] . ' where ' . $fk['from_field'] . ' = :' . $fk['from_field'] . '_orig');
                                if (empty($on_update_value)) {
                                    $Qupdate->bind_null(':' . $fk['from_field']);
                                } else {
                                    $Qupdate->bind_value(':' . $fk['from_field'], $on_update_value);
                                }
                                $Qupdate->bind_value(':' . $fk['from_field'] . '_orig', $Qparent->value($fk['to_field']));
                                $Qupdate->execute();
                            }
                        }
                    }
                }
            }
        }
        return parent::execute($input_parameters);
    }
}