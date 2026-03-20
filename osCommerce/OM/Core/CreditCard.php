<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core;

class Credit_Card
{
    protected $_owner;
    protected $_number;
    protected $_expiry_month;
    protected $_expiry_year;
    protected $_cvc;
    protected $_type;
    protected $_data;
    public function __construct($number = null, $exp_month = null, $exp_year = null)
    {
        $OSCOM_PDO = Registry::get('PDO');
        if (!empty($number)) {
            $this->_number = preg_replace('/[^0-9]/', '', $number);
            $this->_expiry_month = (int) $exp_month;
            $this->_expiry_year = (int) $exp_year;
        }
        $this->_data = [];
        $Qcc = $OSCOM_PDO->query('select id, credit_card_name as title, pattern from :table_credit_cards where credit_card_status = 1 order by sort_order, credit_card_name');
        $Qcc->set_cache('credit_cards');
        $Qcc->execute();
        while ($Qcc->fetch()) {
            $this->_data[$Qcc->value_int('id')] = $Qcc->to_array();
        }
    }
    public function is_valid($valid_cc_types = null)
    {
        if (CFG_CREDIT_CARDS_VERIFY_WITH_REGEXP == '1') {
            if ($this->has_valid_number() === false) {
                return -1;
            }
            if ($this->is_accepted($valid_cc_types) === false) {
                return -5;
            }
        }
        if ($this->has_valid_expiry_date() === false) {
            return -2;
        }
        if ($this->has_expired() === true) {
            return -3;
        }
        if ($this->has_owner() && $this->has_valid_owner() === false) {
            return -4;
        }
        return true;
    }
    public function has_valid_number()
    {
        if (!empty($this->_number) && strlen($this->_number) >= CC_NUMBER_MIN_LENGTH) {
            $card_number = strrev($this->_number);
            $num_sum = 0;
            for ($i = 0, $n = strlen($card_number); $i < $n; $i++) {
                $current_num = substr($card_number, $i, 1);
                // Double every second digit
                if ($i % 2 == 1) {
                    $current_num *= 2;
                }
                // Add digits of 2-digit numbers together
                if ($current_num > 9) {
                    $first_num = $current_num % 10;
                    $second_num = ($current_num - $first_num) / 10;
                    $current_num = $first_num + $second_num;
                }
                $num_sum += $current_num;
            }
            // If the total has no remainder it's OK
            return $num_sum % 10 == 0;
        }
        return false;
    }
    public function is_accepted($valid_cc_types)
    {
        if (!empty($valid_cc_types) && !empty($this->_number) && strlen($this->_number) >= CC_NUMBER_MIN_LENGTH) {
            if (!is_array($valid_cc_types)) {
                $valid_cc_types = explode(',', $valid_cc_types);
            }
            foreach ($this->_data as $data) {
                if (in_array($data['id'], $valid_cc_types)) {
                    if (preg_match($data['pattern'], $this->_number) === 1) {
                        $this->_type = $data['title'];
                        return true;
                    }
                }
            }
        }
        return false;
    }
    public function has_valid_expiry_date()
    {
        $year = date('Y');
        return $this->_expiry_month > 0 && $this->_expiry_month < 13 && $this->_expiry_year >= $year && $this->_expiry_year <= $year + 10;
    }
    public function has_expired()
    {
        return $this->_expiry_year <= date('Y') && $this->_expiry_month < date('n');
    }
    public function has_owner()
    {
        return isset($this->_owner);
    }
    public function has_valid_owner()
    {
        return !empty($this->_owner) && strlen($this->_owner) >= CC_OWNER_MIN_LENGTH;
    }
    public function type_exists($id)
    {
        return isset($this->_data[$id]);
    }
    public function get_number()
    {
        return $this->_number;
    }
    public function get_safe_number()
    {
        return str_repeat('X', strlen($this->_number) - 4) . substr($this->_number, -4);
    }
    public function get_expiry_month()
    {
        return str_pad($this->_expiry_month, 2, '0', STR_PAD_LEFT);
    }
    public function get_expiry_year()
    {
        return $this->_expiry_year;
    }
    public function get_cvc()
    {
        return $this->_cvc;
    }
    public function get_owner()
    {
        return $this->_owner;
    }
    public function get_type_pattern($id)
    {
        return $this->_data[$id]['pattern'];
    }
    public function set_owner($name)
    {
        $this->_owner = trim($name);
    }
    public function set_cvc($cvc)
    {
        $this->_cvc = trim($cvc);
    }
}