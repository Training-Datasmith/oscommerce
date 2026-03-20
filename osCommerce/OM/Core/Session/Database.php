<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Session;

use Os_Commerce\OM\Core\OSCOM;
/**
 * The Session\Database class stores the session data in the database
 *
 * @since v3.0.0
 */
class Database extends \Os_Commerce\OM\Core\Session_Abstract
{
    /**
     * Initialize database based session storage handler
     *
     * @param string $name The name of the session
     * @since v3.0.0
     */
    public function __construct($name)
    {
        $this->set_name($name);
        session_set_save_handler([$this, 'handlerOpen'], [$this, 'handlerClose'], [$this, 'handlerRead'], [$this, 'handlerWrite'], [$this, 'handlerDestroy'], [$this, 'handlerClean']);
    }
    /**
     * Checks if a session exists
     *
     * @param string $id The ID of the session
     * @since v3.0.2
     */
    public function exists($id)
    {
        $data = ['id' => $id];
        return OSCOM::call_db('Session\Database\Check', $data, 'Core');
    }
    /**
     * Opens the database based session storage handler
     *
     * @since v3.0.0
     */
    public function handler_open()
    {
        return true;
    }
    /**
     * Closes the database based session storage handler
     *
     * @since v3.0.0
     */
    public function handler_close()
    {
        return true;
    }
    /**
     * Read session data from the database based session storage handler
     *
     * @param string $id The ID of the session
     * @since v3.0.0
     */
    public function handler_read($id)
    {
        $data = ['id' => $id];
        if ($this->_life_time > 0) {
            $data['expiry'] = time();
        }
        $result = OSCOM::call_db('Session\Database\Get', $data, 'Core');
        if ($result !== false) {
            return base64_decode($result['value']);
        }
        return false;
    }
    /**
     * Writes session data to the database based session storage handler
     *
     * @param string $id The ID of the session
     * @param string $value The session data to store
     * @since v3.0.0
     */
    public function handler_write($id, $value)
    {
        $data = ['id' => $id, 'expiry' => time() + $this->_life_time, 'value' => base64_encode($value)];
        return OSCOM::call_db('Session\Database\Save', $data, 'Core');
    }
    /**
     * Destroys the session data from the database based session storage handler
     *
     * @param string $id The ID of the session
     * @since v3.0.0
     */
    public function handler_destroy($id)
    {
        return $this->delete($id);
    }
    /**
     * Garbage collector for the database based session storage handler
     *
     * @param string $max_life_time The maxmimum time a session should exist
     * @since v3.0.0
     */
    public function handler_clean($max_life_time)
    {
        // $max_life_time is already added to the time in the _custom_write method
        $data = ['expiry' => time()];
        return OSCOM::call_db('Session\Database\DeleteExpired', $data, 'Core');
    }
    /**
     * Deletes the session data from the database based session storage handler
     *
     * @param string $id The ID of the session
     * @since v3.0.0
     */
    public function delete($id = null)
    {
        if (empty($id)) {
            $id = $this->_id;
        }
        $data = ['id' => $id];
        return OSCOM::call_db('Session\Database\Delete', $data, 'Core');
    }
}