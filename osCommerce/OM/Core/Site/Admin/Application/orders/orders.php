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
require 'includes/applications/orders/classes/orders.php';
require 'includes/classes/order.php';
class Os_C_application_orders extends Os_C_template_admin
{
    /* Protected variables */
    protected $_module = 'orders';
    protected $_page_title;
    protected $_page_contents = 'main.php';
    /* Class constructor */
    public function __construct()
    {
        global $os_c_database, $os_c_language, $os_c_message_stack, $os_c_currencies, $orders_statuses, $orders_status_array;
        $this->_page_title = $os_c_language->get('heading_title');
        if (!isset($_GET['action'])) {
            $_GET['action'] = '';
        }
        if (!isset($_GET['page']) || isset($_GET['page']) && !is_numeric($_GET['page'])) {
            $_GET['page'] = 1;
        }
        include '../includes/classes/currencies.php';
        $os_c_currencies = new Os_C_currencies();
        $orders_statuses = [];
        $orders_status_array = [];
        $Qstatuses = $os_c_database->query('select orders_status_id, orders_status_name from :table_orders_status where language_id = :language_id');
        $Qstatuses->bind_table(':table_orders_status', TABLE_ORDERS_STATUS);
        $Qstatuses->bind_int(':language_id', $os_c_language->get_id());
        $Qstatuses->execute();
        while ($Qstatuses->next()) {
            $orders_statuses[] = ['id' => $Qstatuses->value_int('orders_status_id'), 'text' => $Qstatuses->value('orders_status_name')];
            $orders_status_array[$Qstatuses->value_int('orders_status_id')] = $Qstatuses->value('orders_status_name');
        }
        if (!empty($_GET['action'])) {
            switch ($_GET['action']) {
                case 'invoice':
                    $this->_page_contents = 'invoice.php';
                    $this->_has_header = false;
                    $this->_has_footer = false;
                    break;
                case 'packaging_slip':
                    $this->_page_contents = 'packaging_slip.php';
                    $this->_has_header = false;
                    $this->_has_footer = false;
                    break;
                case 'save':
                    $this->_page_contents = 'edit.php';
                    break;
                case 'updateTransaction':
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        if ($this->_update_transaction($_GET['oID'], $_POST['transaction'])) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&oID=' . $_GET['oID'] . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&action=save&tabIndex=tabTransactionHistory'));
                    }
                    break;
                case 'updateStatus':
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        $data = ['status_id' => $_POST['status'], 'comment' => $_POST['comment'], 'notify_customer' => isset($_POST['notify_customer']) && $_POST['notify_customer'] == 'on' ? true : false, 'append_comment' => isset($_POST['append_comment']) && $_POST['append_comment'] == 'on' ? true : false];
                        if ($this->_update_status($_GET['oID'], $data)) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&oID=' . $_GET['oID'] . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&action=save&tabIndex=tabStatusHistory'));
                    }
                    break;
                case 'delete':
                    $this->_page_contents = 'delete.php';
                    if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                        if (Os_C_orders_admin::delete($_GET['oID'], isset($_POST['restock']) && $_POST['restock'] == 'on' ? true : false)) {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                        } else {
                            $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                        }
                        osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page']));
                    }
                    break;
                case 'batchDelete':
                    if (isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch'])) {
                        $this->_page_contents = 'batch_delete.php';
                        if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
                            $error = false;
                            foreach ($_POST['batch'] as $id) {
                                if (!Os_C_orders_admin::delete($id, isset($_POST['restock']) && $_POST['restock'] == 'on' ? true : false)) {
                                    $error = true;
                                    break;
                                }
                            }
                            if ($error === false) {
                                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                            } else {
                                $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                            }
                            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page']));
                        }
                    }
                    break;
            }
        }
    }
    /* Private methods */
    public function _update_transaction($id, $call_function)
    {
        global $os_c_database;
        $Qorder = $os_c_database->query('select payment_module from :table_orders where orders_id = :orders_id limit 1');
        $Qorder->bind_table(':table_orders', TABLE_ORDERS);
        $Qorder->bind_int(':orders_id', $id);
        $Qorder->execute();
        if ($Qorder->number_of_rows() === 1 && !osc_empty($Qorder->value('payment_module'))) {
            if (file_exists('includes/modules/payment/' . $Qorder->value('payment_module') . '.php')) {
                include 'includes/classes/payment.php';
                include 'includes/modules/payment/' . $Qorder->value('payment_module') . '.php';
                if (is_callable(['osC_Payment_' . $Qorder->value('payment_module'), $call_function])) {
                    $payment_module = 'osC_Payment_' . $Qorder->value('payment_module');
                    $payment_module = new $payment_module();
                    $payment_module->{$call_function}($id);
                    // HPDL - the following static call won't work due to using $this->_gateway_url in the class method
                    //            call_user_func(array('osC_Payment_' . $Qorder->value('payment_module'), $call_function), $id);
                    return true;
                }
            }
        }
        return false;
    }
    public function _update_status($id, $data)
    {
        global $os_c_database, $os_c_language, $orders_status_array;
        $error = false;
        $os_c_database->start_transaction();
        $Qorder = $os_c_database->query('select customers_name, customers_email_address, orders_status, date_purchased from :table_orders where orders_id = :orders_id');
        $Qorder->bind_table(':table_orders', TABLE_ORDERS);
        $Qorder->bind_int(':orders_id', $id);
        $Qorder->execute();
        $Qupdate = $os_c_database->query('update :table_orders set orders_status = :orders_status, last_modified = now() where orders_id = :orders_id');
        $Qupdate->bind_table(':table_orders', TABLE_ORDERS);
        $Qupdate->bind_int(':orders_status', $data['status_id']);
        $Qupdate->bind_int(':orders_id', $id);
        $Qupdate->set_logging($_SESSION['module'], $id);
        $Qupdate->execute();
        if (!$os_c_database->is_error()) {
            if ($data['notify_customer'] === true) {
                $email_body = sprintf($os_c_language->get('email_body'), STORE_NAME, $id, osc_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $id, 'SSL', false, false, true), Os_C_date_Time::get_long($Qorder->value('date_purchased'))) . "\n\n";
                if ($data['append_comment'] === true) {
                    $email_body .= sprintf($os_c_language->get('email_body_comment'), $data['comment']) . "\n\n";
                }
                $email_body .= sprintf($os_c_language->get('email_body_status'), $orders_status_array[$data['status_id']]) . "\n\n" . $os_c_language->get('email_body_contact');
                osc_email($Qorder->value('customers_name'), $Qorder->value('customers_email_address'), sprintf($os_c_language->get('email_subject'), STORE_NAME), $email_body, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
            }
            $Qupdate = $os_c_database->query('insert into :table_orders_status_history (orders_id, orders_status_id, date_added, customer_notified, comments) values (:orders_id, :orders_status_id, now(), :customer_notified, :comments)');
            $Qupdate->bind_table(':table_orders_status_history', TABLE_ORDERS_STATUS_HISTORY);
            $Qupdate->bind_int(':orders_id', $id);
            $Qupdate->bind_int(':orders_status_id', $data['status_id']);
            $Qupdate->bind_int(':customer_notified', $data['notify_customer'] === true ? '1' : '0');
            $Qupdate->bind_value(':comments', $data['comment']);
            $Qupdate->set_logging($_SESSION['module'], $id);
            $Qupdate->execute();
            if ($os_c_database->is_error()) {
                $error = true;
            }
        } else {
            $error = true;
        }
        if ($error === false) {
            $os_c_database->commit_transaction();
            return true;
        }
        $os_c_database->rollback_transaction();
        return false;
    }
}