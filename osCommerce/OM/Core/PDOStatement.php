<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core;

/**
 * Represents a prepared statement and, after the statement is executed, an
 * associated result set.
 *
 * @since v3.0.0
 */
class PDOStatement extends \PDOStatement
{
    protected $_is_error = false;
    protected $_binded_params = [];
    protected $_cache_key;
    protected $_cache_expire;
    protected $_cache_data;
    protected $_cache_read = false;
    public function bind_value($parameter, $value, $data_type = PDO::PARAM_STR)
    {
        $this->_binded_params[$parameter] = ['value' => $value, 'data_type' => $data_type];
        return parent::bind_value($parameter, $value, $data_type);
    }
    public function bind_int($parameter, $value)
    {
        // force type to int (see http://bugs.php.net/bug.php?id=44639)
        return $this->bind_value($parameter, (int) $value, PDO::PARAM_INT);
    }
    public function bind_bool($parameter, $value)
    {
        // force type to bool (see http://bugs.php.net/bug.php?id=44639)
        return $this->bind_value($parameter, (bool) $value, PDO::PARAM_BOOL);
    }
    public function bind_null($parameter)
    {
        return $this->bind_value($parameter, null, PDO::PARAM_NULL);
    }
    public function execute($input_parameters = [])
    {
        if (isset($this->_cache_key)) {
            if (Registry::get('Cache')->read($this->_cache_key, $this->_cache_expire)) {
                $this->_cache_data = Registry::get('Cache')->get_cache();
                $this->_cache_read = true;
            }
        }
        if ($this->_cache_read === false) {
            if (empty($input_parameters)) {
                $input_parameters = null;
            }
            $this->_is_error = !parent::execute($input_parameters);
            if ($this->_is_error === true) {
                trigger_error($this->query_string);
            }
        }
    }
    public function fetch($fetch_style = PDO::FETCH_ASSOC, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0)
    {
        if ($this->_cache_read === true) {
            list(, $this->result) = each($this->_cache_data);
        } else {
            $this->result = parent::fetch($fetch_style, $cursor_orientation, $cursor_offset);
            if (isset($this->_cache_key)) {
                $this->_cache_data[] = $this->result;
            }
        }
        return $this->result;
    }
    public function fetch_all($fetch_style = PDO::FETCH_ASSOC, $fetch_argument = null, $ctor_args = [])
    {
        if ($this->_cache_read === true) {
            $this->result = $this->_cache_data;
        } else {
            // fetchAll() fails if second argument is passed in a fetch style that does not
            // use the optional argument
            if (in_array($fetch_style, [PDO::FETCH_COLUMN, PDO::FETCH_CLASS, PDO::FETCH_FUNC])) {
                $this->result = parent::fetch_all($fetch_style, $fetch_argument, $ctor_args);
            } else {
                $this->result = parent::fetch_all($fetch_style);
            }
            if (isset($this->_cache_key)) {
                $this->_cache_data = $this->result;
            }
        }
        return $this->result;
    }
    public function to_array()
    {
        if (!isset($this->result)) {
            $this->fetch();
        }
        return $this->result;
    }
    public function set_cache($key, $expire = 0)
    {
        $this->_cache_key = $key;
        $this->_cache_expire = $expire;
    }
    protected function value_mixed($column, $type = 'string')
    {
        if (!isset($this->result)) {
            $this->fetch();
        }
        switch ($type) {
            case 'protected':
                return HTML::output_protected($this->result[$column]);
                break;
            case 'int':
                return (int) $this->result[$column];
                break;
            case 'decimal':
                return (float) $this->result[$column];
                break;
            case 'string':
            default:
                return $this->result[$column];
        }
    }
    public function value($column)
    {
        return $this->value_mixed($column, 'string');
    }
    public function value_protected($column)
    {
        return $this->value_mixed($column, 'protected');
    }
    public function value_int($column)
    {
        return $this->value_mixed($column, 'int');
    }
    public function value_decimal($column)
    {
        return $this->value_mixed($column, 'decimal');
    }
    public function is_error()
    {
        return $this->_is_error;
    }
    /**
     * Return the query string
     *
     * @return string
     * @since v3.0.2
     */
    public function get_query()
    {
        return $this->query_string;
    }
    public function __destruct()
    {
        if ($this->_cache_read === false) {
            if (isset($this->_cache_key)) {
                Registry::get('Cache')->write($this->_cache_data, $this->_cache_key);
            }
        }
    }
}